<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Models\Concerns\EnforcesVendorIsolation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReturnRequest extends Model
{
    use HasFactory, EnforcesVendorIsolation;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_REFUNDED = 'refunded';

    public const LIFECYCLE = [
        self::STATUS_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_PICKED_UP,
        self::STATUS_REFUNDED,
    ];

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_REQUESTED => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED => [self::STATUS_PICKED_UP, self::STATUS_REJECTED],
        self::STATUS_REJECTED => [],
        self::STATUS_PICKED_UP => [self::STATUS_REFUNDED],
        self::STATUS_REFUNDED => [],
    ];

    protected $fillable = [
        'rma_number',
        'order_id',
        'user_id',
        'vendor_id',
        'status',
        'reason',
        'details',
        'requested_refund_amount',
        'approved_refund_amount',
        'rejection_reason',
        'pickup_note',
        'refund_method',
        'refund_transaction_id',
        'processed_by',
        'approved_at',
        'rejected_at',
        'picked_up_at',
        'refunded_at',
    ];

    protected $casts = [
        'requested_refund_amount' => 'decimal:2',
        'approved_refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $returnRequest): void {
            if (empty($returnRequest->rma_number)) {
                $returnRequest->rma_number = self::generateRmaNumber();
            }

            if (empty($returnRequest->status)) {
                $returnRequest->status = self::STATUS_REQUESTED;
            }
        });
    }

    public static function generateRmaNumber(): string
    {
        do {
            $rma = 'RMA-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (self::query()->where('rma_number', $rma)->exists());

        return $rma;
    }

    public static function statusLabel(string $status): string
    {
        return match (strtolower(trim($status))) {
            self::STATUS_REQUESTED => 'Requested',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PICKED_UP => 'Picked Up',
            self::STATUS_REFUNDED => 'Refunded',
            default => ucfirst(str_replace('_', ' ', strtolower(trim($status)))),
        };
    }

    public static function allStatuses(): array
    {
        return self::LIFECYCLE;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReturnRequestStatusHistory::class);
    }

    public function getAllowedNextStatuses(): array
    {
        return self::ALLOWED_TRANSITIONS[$this->status] ?? [];
    }

    public function canTransitionTo(string $status): bool
    {
        $status = strtolower(trim($status));

        if ($status === $this->status) {
            return true;
        }

        return in_array($status, $this->getAllowedNextStatuses(), true);
    }

    public function updateStatus(string $status, ?User $actor = null, ?string $comment = null, bool $notifyCustomer = true): void
    {
        $status = strtolower(trim($status));

        if (!in_array($status, self::LIFECYCLE, true)) {
            throw new InvalidArgumentException("Invalid return request status: {$status}");
        }

        if ($status === $this->status) {
            return;
        }

        if (!$this->canTransitionTo($status)) {
            $allowed = implode(', ', array_map(fn($next) => self::statusLabel($next), $this->getAllowedNextStatuses()));
            throw new InvalidArgumentException(
                'Invalid return status transition from ' . self::statusLabel($this->status) .
                ' to ' . self::statusLabel($status) .
                ($allowed ? ". Allowed: {$allowed}." : '.')
            );
        }

        $oldStatus = $this->status;
        $this->status = $status;

        if ($actor) {
            $this->processed_by = $actor->id;
        }

        match ($status) {
            self::STATUS_APPROVED => $this->approved_at = now(),
            self::STATUS_REJECTED => $this->rejected_at = now(),
            self::STATUS_PICKED_UP => $this->picked_up_at = now(),
            self::STATUS_REFUNDED => $this->refunded_at = now(),
            default => null,
        };

        $this->save();

        $this->statusHistories()->create([
            'user_id' => $actor?->id,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'comment' => $comment,
            'notify_customer' => $notifyCustomer,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel((string) $this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_PICKED_UP => 'secondary',
            self::STATUS_REFUNDED => 'success',
            default => 'secondary',
        };
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, [self::STATUS_REJECTED, self::STATUS_REFUNDED], true);
    }
}

