<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(): View
    {


        
        $purchaseOrders = PurchaseOrder::with('supplier', 'createdBy')
            ->latest()
            ->paginate(10);
        
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(): View
    {


        
        $suppliers = Supplier::all();
        return view('purchase-orders.create', compact('suppliers'));
    }

    /**
     * Store a newly created purchase order in storage.
     */
    public function store(Request $request): RedirectResponse
    {


        
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['po_number'] = PurchaseOrder::generatePoNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $purchaseOrder = PurchaseOrder::create($validated);

        return redirect()->route('purchase-orders.edit', $purchaseOrder)
            ->with('success', 'PO berhasil dibuat');
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {


        
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit(PurchaseOrder $purchaseOrder): View
    {


        
        $suppliers = Supplier::all();
        $items = Item::all();
        
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    /**
     * Update the specified purchase order in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {


        
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('purchase-orders.edit', $purchaseOrder)
            ->with('success', 'PO berhasil diupdate');
    }

    /**
     * Add item to purchase order.
     */
    public function addItem(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {


        
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.edit', $purchaseOrder)
                ->with('error', 'Hanya PO draft yang bisa ditambahkan item');
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::find($validated['item_id']);
        $subtotal = $validated['quantity'] * $item->unit_price;

        $purchaseOrder->details()->create([
            'item_id' => $validated['item_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $item->unit_price,
            'subtotal' => $subtotal,
        ]);

        // Update total amount
        $total = $purchaseOrder->details()->sum('subtotal');
        $purchaseOrder->update(['total_amount' => $total]);

        return redirect()->route('purchase-orders.edit', $purchaseOrder)
            ->with('success', 'Item berhasil ditambahkan');
    }

    /**
     * Remove item from purchase order.
     */
    public function removeItem(PurchaseOrder $purchaseOrder, PurchaseOrderDetail $detail): RedirectResponse
    {


        
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.edit', $purchaseOrder)
                ->with('error', 'Hanya PO draft yang bisa menghapus item');
        }

        $detail->delete();

        // Update total amount
        $total = $purchaseOrder->details()->sum('subtotal');
        $purchaseOrder->update(['total_amount' => $total]);

        return redirect()->route('purchase-orders.edit', $purchaseOrder)
            ->with('success', 'Item berhasil dihapus');
    }

    /**
     * Confirm/Submit purchase order.
     */
    public function confirm(PurchaseOrder $purchaseOrder): RedirectResponse
    {


        
        if (!$purchaseOrder->canConfirm()) {
            return redirect()->route('purchase-orders.edit', $purchaseOrder)
                ->with('error', 'PO harus memiliki minimal 1 item');
        }

        $purchaseOrder->update(['status' => 'pending']);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'PO berhasil dikonfirmasi');
    }

    /**
     * Receive/Complete purchase order (Barang masuk).
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {


        
        if (!$purchaseOrder->canReceive()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'PO harus dalam status pending untuk diterima');
        }

        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        try {
            DB::beginTransaction();

            // Process setiap item di PO
            foreach ($purchaseOrder->details as $detail) {
                // Cek atau buat stock record
                $stock = Stock::firstOrCreate(
                    [
                        'item_id' => $detail->item_id,
                        'location_id' => $validated['location_id'],
                    ],
                    ['quantity' => 0]
                );

                // Update quantity
                $stock->increment('quantity', $detail->quantity);
                $stock->update(['last_updated' => now()]);

                // Catat movement
                StockMovement::create([
                    'reference_number' => $purchaseOrder->po_number,
                    'reference_type' => 'PURCHASE_ORDER',
                    'type' => 'IN',
                    'item_id' => $detail->item_id,
                    'location_id' => $validated['location_id'],
                    'quantity' => $detail->quantity,
                    'created_by' => Auth::id(),
                    'notes' => 'Barang masuk dari PO',
                ]);
            }

            // Update PO status
            $purchaseOrder->update(['status' => 'received']);

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Barang masuk berhasil diterima');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Gagal menerima barang: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase order from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {


        
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Hanya PO draft yang bisa dihapus');
        }

        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')
            ->with('success', 'PO berhasil dihapus');
    }
}
