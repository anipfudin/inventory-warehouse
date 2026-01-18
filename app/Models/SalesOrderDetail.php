<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderDetail extends Model
{
    protected $fillable = [
        'sales_order_id',
        'item_id',
        'quantity_requested',
        'quantity_shipped',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Check if item can be shipped (stock availability)
     */
    public function canShip(): bool
    {
        $availableStock = $this->item->getTotalStock();
        return $availableStock >= $this->quantity_requested;
    }
}
