<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'item_number',
        'name',
        'description',
        'unit',
        'supplier_id',
        'unit_price',
        'minimum_stock',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function salesOrderDetails(): HasMany
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get total stock across all locations
     */
    public function getTotalStock(): int
    {
        return $this->stocks()->sum('quantity');
    }
}
