<?php

namespace App\Models;

use App\Domains\ECommerce\Models\Concerns\EnforcesVendorIsolation;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VendorPayout extends Model
{
    use HasFactory, EnforcesVendorIsolation;

    protected $fillable = [
        'vendor_id',
        'payout_number',
        'amount',
        'platform_fee',
        'net_amount',
        'payment_method',
        'payment_details',
        'status',
        'notes',
        'reference_number',
        'period_start',
        'period_end',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_number)) {
                $payout->payout_number = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(VendorPayoutItem::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function markAsProcessed(User $processor, ?string $referenceNumber = null): void
    {
        $oldValues = [
            'status' => (string) $this->status,
            'processed_at' => $this->processed_at?->toDateTimeString(),
            'processed_by' => $this->processed_by,
            'reference_number' => (string) $this->reference_number,
        ];

        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => $processor->id,
            'reference_number' => $referenceNumber,
        ]);

        app(AuditLogService::class)->log(
            'payout.approved',
            $this,
            $oldValues,
            [
                'status' => (string) $this->status,
                'processed_at' => $this->processed_at?->toDateTimeString(),
                'processed_by' => $this->processed_by,
                'reference_number' => (string) $this->reference_number,
            ],
            [
                'payout_number' => (string) $this->payout_number,
                'vendor_id' => (int) ($this->vendor_id ?? 0),
                'net_amount' => (float) ($this->net_amount ?? 0),
            ],
            $processor,
            (int) ($this->vendor_id ?? 0),
        );
    }

    public function markLedgerItemsPosted(User $processor): int
    {
        $timestamp = now();

        return $this->items()
            ->whereNull('posted_at')
            ->update([
                'posted_at' => $timestamp,
                'posted_by' => $processor->id,
                'updated_at' => $timestamp,
            ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getLedgerOrderAmountAttribute(): float
    {
        return (float) $this->items()->sum('order_total');
    }

    public function getLedgerCommissionAmountAttribute(): float
    {
        return (float) $this->items()->sum('commission_amount');
    }

    public function getLedgerRefundAmountAttribute(): float
    {
        return (float) $this->items()->sum('refund_amount');
    }

    public function getLedgerPayableAmountAttribute(): float
    {
        return (float) $this->items()->sum('payable_amount');
    }

    public function getLedgerPostedItemsCountAttribute(): int
    {
        if ($this->relationLoaded('items')) {
            return $this->items->whereNotNull('posted_at')->count();
        }

        return (int) $this->items()->whereNotNull('posted_at')->count();
    }

    public function getLedgerPendingItemsCountAttribute(): int
    {
        if ($this->relationLoaded('items')) {
            return $this->items->whereNull('posted_at')->count();
        }

        return (int) $this->items()->whereNull('posted_at')->count();
    }
}
