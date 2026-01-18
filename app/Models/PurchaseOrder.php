<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_id',
        'status',
        'delivery_date',
        'total_amount',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Generate unique PO number
     */
    public static function generatePoNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;
        
        return 'PO-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if PO can be confirmed (has at least 1 item)
     */
    public function canConfirm(): bool
    {
        return $this->details()->count() > 0 && $this->status === 'draft';
    }

    /**
     * Check if PO can be received
     */
    public function canReceive(): bool
    {
        return $this->status === 'pending';
    }
}
