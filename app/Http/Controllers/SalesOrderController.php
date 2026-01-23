<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of sales orders.
     */
    public function index(): View
    {
        $user = Auth::user();
        $query = SalesOrder::with('createdBy');

        // User hanya bisa lihat SO mereka sendiri
        if (!$user->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $salesOrders = $query->latest()->paginate(10);

        return view('sales-orders.index', compact('salesOrders'));
    }

    /**
     * Show the form for creating a new sales order.
     */
    public function create(): View
    {
        $items = Item::all();
        return view('sales-orders.create', compact('items'));
    }

    /**
     * Store a newly created sales order in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['so_number'] = SalesOrder::generateSoNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $salesOrder = SalesOrder::create($validated);

        return redirect()->route('sales-orders.edit', $salesOrder)
            ->with('success', 'SO berhasil dibuat');
    }

    /**
     * Display the specified sales order.
     */
    public function show(SalesOrder $salesOrder): View
    {
        $this->authorizeViewSalesOrder($salesOrder);

        return view('sales-orders.show', compact('salesOrder'));
    }

    /**
     * Show the form for editing the specified sales order.
     */
    public function edit(SalesOrder $salesOrder): View
    {
        $this->authorizeViewSalesOrder($salesOrder);

        $items = Item::all();

        return view('sales-orders.edit', compact('salesOrder', 'items'));
    }

    /**
     * Update the specified sales order in storage.
     */
    public function update(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        $validated = $request->validate([
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $salesOrder->update($validated);

        return redirect()->route('sales-orders.edit', $salesOrder)
            ->with('success', 'SO berhasil diupdate');
    }

    /**
     * Add item to sales order.
     */
    public function addItem(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('error', 'Hanya SO draft yang bisa ditambahkan item');
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::find($validated['item_id']);

        // Validasi stock (hanya validasi ketika mengambil barang)
        $totalStock = $item->getTotalStock();
        if ($totalStock < $validated['quantity']) {
            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('error', "Stock tidak cukup untuk item {$item->name}. Stock tersedia: {$totalStock}");
        }

        $subtotal = $validated['quantity'] * $item->unit_price;

        $salesOrder->details()->create([
            'item_id' => $validated['item_id'],
            'quantity_requested' => $validated['quantity'],
            'quantity_shipped' => 0,
            'unit_price' => $item->unit_price,
            'subtotal' => $subtotal,
        ]);

        // Update total amount
        $total = $salesOrder->details()->sum('subtotal');
        $salesOrder->update(['total_amount' => $total]);

        return redirect()->route('sales-orders.edit', $salesOrder)
            ->with('success', 'Item berhasil ditambahkan');
    }

    /**
     * Remove item from sales order.
     */
    public function removeItem(SalesOrder $salesOrder, int $detail_id): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('error', 'Hanya SO draft yang bisa menghapus item');
        }

        $detail = SalesOrderDetail::findOrFail($detail_id);
        $detail->delete();

        // Update total amount
        $total = $salesOrder->details()->sum('subtotal');
        $salesOrder->update(['total_amount' => $total]);

        return redirect()->route('sales-orders.edit', $salesOrder)
            ->with('success', 'Item berhasil dihapus');
    }

    /**
     * Confirm/Submit sales order.
     */
    public function confirm(SalesOrder $salesOrder): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        if (!$salesOrder->canConfirm()) {
            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('error', 'SO harus memiliki minimal 1 item');
        }

        // Validasi semua item punya stock
        if (!$salesOrder->canShipAll()) {
            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('error', 'Ada item yang stock tidak cukup untuk dikirim');
        }

        $salesOrder->update(['status' => 'pending']);

        return redirect()->route('sales-orders.index')
            ->with('success', 'SO berhasil dikonfirmasi');
    }

    /**
     * Cancel a sales order.
     */
    public function cancel(SalesOrder $salesOrder): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        if ($salesOrder->status === 'shipped') {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'SO yang sudah dikirim tidak bisa dibatalkan');
        }

        $salesOrder->update(['status' => 'cancelled']);

        return redirect()->route('sales-orders.index')
            ->with('success', 'SO berhasil dibatalkan');
    }

    /**
     * Ship/Complete sales order (Barang keluar).
     */
    public function ship(Request $request, SalesOrder $salesOrder): RedirectResponse
    {



        if (!$salesOrder->canShip()) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'SO harus dalam status pending untuk dikirim');
        }

        try {
            DB::beginTransaction();

            // Process setiap item di SO
            foreach ($salesOrder->details as $detail) {
                $itemStock = $detail->quantity_requested;
                $remaining = $itemStock;

                // Ambil dari berbagai lokasi sampai quantity terpenuhi
                $stocks = Stock::where('item_id', $detail->item_id)
                    ->where('quantity', '>', 0)
                    ->get();

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) break;

                    $amountToTake = min($stock->quantity, $remaining);
                    $stock->decrement('quantity', $amountToTake);
                    $stock->update(['last_updated' => now()]);

                    // Catat movement
                    StockMovement::create([
                        'reference_number' => $salesOrder->so_number,
                        'reference_type' => 'SALES_ORDER',
                        'type' => 'OUT',
                        'item_id' => $detail->item_id,
                        'location_id' => $stock->location_id,
                        'quantity' => $amountToTake,
                        'created_by' => Auth::id(),
                        'notes' => 'Barang keluar untuk SO',
                    ]);

                    $remaining -= $amountToTake;
                }

                // Update SO detail shipped quantity
                $detail->update(['quantity_shipped' => $itemStock]);
            }

            // Update SO status
            $salesOrder->update(['status' => 'shipped']);

            DB::commit();

            return redirect()->route('sales-orders.index')
                ->with('success', 'Barang keluar berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Gagal memproses barang keluar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sales order from storage.
     */
    public function destroy(SalesOrder $salesOrder): RedirectResponse
    {
        $this->authorizeViewSalesOrder($salesOrder);

        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Hanya SO draft yang bisa dihapus');
        }

        $salesOrder->delete();
        return redirect()->route('sales-orders.index')
            ->with('success', 'SO berhasil dihapus');
    }

    /**
     * Authorize viewing sales order.
     */
    private function authorizeViewSalesOrder(SalesOrder $salesOrder): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $salesOrder->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
