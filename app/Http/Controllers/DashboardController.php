<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        // Statistics
        $totalItems = Item::count();
        $totalSuppliers = Supplier::count();
        $totalUsers = User::count();
        $totalLocations = Location::count();
        
        // Calculate total stock value
        $totalStockValue = Stock::with('item')
            ->get()
            ->sum(function ($stock) {
                return $stock->quantity * $stock->item->unit_price;
            });
        
        // Pending transactions
        $pendingPO = PurchaseOrder::where('status', 'pending')->count();
        $pendingSO = SalesOrder::where('status', 'pending')->count();
        
        // Recent stock movements (last 10)
        $recentMovements = StockMovement::with(['item', 'location', 'createdBy'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Low stock items (stok kurang dari minimum)
        $lowStockItems = Item::with('stocks')
            ->get()
            ->filter(function ($item) {
                return $item->getTotalStock() < $item->minimum_stock;
            })
            ->take(5);

        return view('dashboard', compact(
            'totalItems',
            'totalSuppliers',
            'totalUsers',
            'totalLocations',
            'totalStockValue',
            'pendingPO',
            'pendingSO',
            'recentMovements',
            'lowStockItems'
        ));
    }
}
