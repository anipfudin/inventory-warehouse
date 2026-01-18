<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data - View only untuk user biasa
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/{location}', [LocationController::class, 'show'])->name('locations.show');
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Purchase Orders - View only untuk user biasa
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');

    // Sales Orders - Full CRUD untuk user biasa
    Route::resource('sales-orders', SalesOrderController::class);
    Route::post('sales-orders/{sales_order}/add-item', [SalesOrderController::class, 'addItem'])->name('sales-orders.add-item');
    Route::post('sales-orders/{sales_order}/remove-item/{detail_id}', [SalesOrderController::class, 'removeItem'])->name('sales-orders.remove-item');
    Route::post('sales-orders/{sales_order}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
    Route::post('sales-orders/{sales_order}/cancel', [SalesOrderController::class, 'cancel'])->name('sales-orders.cancel');
    Route::post('sales-orders/{sales_order}/ship', [SalesOrderController::class, 'ship'])->name('sales-orders.ship');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('items', ItemController::class);
        
        // Purchase Orders - Full CRUD untuk admin
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('purchase-orders/{purchase_order}/add-item', [PurchaseOrderController::class, 'addItem'])->name('purchase-orders.add-item');
        Route::post('purchase-orders/{purchase_order}/remove-item/{detail_id}', [PurchaseOrderController::class, 'removeItem'])->name('purchase-orders.remove-item');
        Route::post('purchase-orders/{purchase_order}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
        Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    });
});

require __DIR__.'/auth.php';
