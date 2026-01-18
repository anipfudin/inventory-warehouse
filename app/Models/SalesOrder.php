<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $fillable = [
        'so_number',
        'status',
        'required_date',
        'total_amount',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'required_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    /**
     * Generate unique SO number
     */
    public static function generateSoNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;
        
        return 'SO-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if SO can be confirmed (has at least 1 item)
     */
    public function canConfirm(): bool
    {
        return $this->details()->count() > 0 && $this->status === 'draft';
    }

    /**
     * Check if SO can be shipped
     */
    public function canShip(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if all items can be shipped (stock availability validation)
     */
    public function canShipAll(): bool
    {
        foreach ($this->details as $detail) {
            $totalStock = $detail->item->getTotalStock();
            if ($totalStock < $detail->quantity_requested) {
                return false;
            }
        }
        return true;
    }
}
