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

  
    public static function generatePoNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'PO-' . $year . $month . '-';
        
        // Get the highest sequence number used this month
        $lastPo = self::where('po_number', 'like', $prefix . '%')
                    ->orderBy('po_number', 'desc')
                    ->first();
        
        if ($lastPo) {
            $lastNumber = (int) substr($lastPo->po_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function canConfirm(): bool
    {
        return $this->details()->count() > 0 && $this->status === 'draft';
    }


    public function canReceive(): bool
    {
        return $this->status === 'pending';
    }
}
