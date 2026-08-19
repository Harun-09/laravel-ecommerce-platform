<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayoutItem extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function (self $item) {
            if (!$item->order_id) {
                return;
            }

            $order = Order::find($item->order_id);
            if (!$order) {
                return;
            }

            $item->order_total = $item->order_total ?? (float) $order->total;
            $item->commission_amount = $item->commission_amount ?? (float) $order->commission_amount;
            $item->refund_amount = $item->refund_amount ?? (float) ($order->refunded_amount ?? 0);
            $item->vendor_earning = $item->vendor_earning ?? (float) $order->vendor_earning;
            $item->payable_amount = $item->payable_amount ?? $item->recalculatePayable();
        });
    }

    protected $fillable = [
        'vendor_payout_id',
        'order_id',
        'order_total',
        'commission_amount',
        'refund_amount',
        'vendor_earning',
        'payable_amount',
        'posted_at',
        'posted_by',
    ];

    protected $casts = [
        'order_total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'vendor_earning' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    public function payout()
    {
        return $this->belongsTo(VendorPayout::class, 'vendor_payout_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function recalculatePayable(): float
    {
        $payable = (float) $this->order_total - (float) $this->commission_amount - (float) ($this->refund_amount ?? 0);
        return round(max(0, $payable), 2);
    }

    public function getIsPostedAttribute(): bool
    {
        return $this->posted_at !== null;
    }
}
