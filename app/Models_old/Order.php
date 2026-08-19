<?php

namespace App\Models;

use App\Domains\ECommerce\Models\Concerns\EnforcesVendorIsolation;
use App\Services\AuditLogService;
use App\Services\OrderNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes, EnforcesVendorIsolation;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RETURNED = 'returned';

    public const LEGACY_STATUS_MAP = [
        'confirmed' => self::STATUS_PAID,
        'out_for_delivery' => self::STATUS_SHIPPED,
        'canceled' => self::STATUS_CANCELLED,
    ];

    public const LIFECYCLE = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
        self::STATUS_RETURNED,
    ];

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_PAID, self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED => [self::STATUS_DELIVERED, self::STATUS_RETURNED],
        self::STATUS_DELIVERED => [self::STATUS_RETURNED],
        self::STATUS_CANCELLED => [],
        self::STATUS_RETURNED => [],
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'vendor_id',
        'checkout_token',
        'coupon_id',
        'status',
        'payment_status',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'cod_fee',
        'tax_amount',
        'total',
        'refunded_amount',
        'commission_rate',
        'commission_amount',
        'vendor_earning',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_city',
        'delivery_zone',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_method',
        'billing_name',
        'billing_phone',
        'billing_address',
        'billing_city',
        'payment_method',
        'transaction_id',
        'tracking_number',
        'shipping_carrier',
        'customer_notes',
        'admin_notes',
        'cancellation_reason',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'cod_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_earning' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        return "{$prefix}-{$date}-{$random}";
    }

    public static function allStatuses(bool $includeLegacy = false): array
    {
        if (!$includeLegacy) {
            return self::LIFECYCLE;
        }

        return array_values(array_unique([
            ...self::LIFECYCLE,
            ...array_keys(self::LEGACY_STATUS_MAP),
        ]));
    }

    public static function lifecycleOrder(): array
    {
        return self::LIFECYCLE;
    }

    public static function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return self::LEGACY_STATUS_MAP[$status] ?? $status;
    }

    public static function statusLabel(string $status): string
    {
        return match (self::normalizeStatus($status)) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Canceled',
            self::STATUS_RETURNED => 'Returned',
            default => ucfirst(str_replace('_', ' ', self::normalizeStatus($status))),
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function latestReturnRequest()
    {
        return $this->hasOne(ReturnRequest::class)->latestOfMany();
    }

    public function payoutItems()
    {
        return $this->hasMany(VendorPayoutItem::class);
    }

    public function postedPayoutItems()
    {
        return $this->hasMany(VendorPayoutItem::class)->whereNotNull('posted_at');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query): Builder
    {
        // Legacy alias: old "confirmed" stage now maps to "paid"
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePaidStatus($query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Status Methods
    public function getAllowedNextStatuses(): array
    {
        $currentStatus = self::normalizeStatus((string) $this->status);
        return self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
    }

    public function canTransitionTo(string $status): bool
    {
        $normalizedStatus = self::normalizeStatus($status);
        $currentStatus = self::normalizeStatus((string) $this->status);

        if ($normalizedStatus === $currentStatus) {
            return true;
        }

        return in_array($normalizedStatus, $this->getAllowedNextStatuses(), true);
    }

    public function updateStatus(string $status, ?User $user = null, ?string $comment = null, bool $notifyCustomer = false): void
    {
        $normalizedStatus = self::normalizeStatus($status);
        $currentStatus = self::normalizeStatus((string) $this->status);

        if (!in_array($normalizedStatus, self::LIFECYCLE, true)) {
            throw new InvalidArgumentException("Invalid order status: {$status}");
        }

        if ($normalizedStatus === $currentStatus) {
            return;
        }

        if (!$this->canTransitionTo($normalizedStatus)) {
            $allowed = implode(', ', array_map(fn($s) => self::statusLabel($s), $this->getAllowedNextStatuses()));
            throw new InvalidArgumentException(
                "Invalid status transition from " . self::statusLabel($currentStatus) .
                " to " . self::statusLabel($normalizedStatus) .
                ($allowed ? ". Allowed: {$allowed}." : '.')
            );
        }

        // For online payments, payment must be completed before operational fulfillment
        if (
            in_array($normalizedStatus, [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_DELIVERED], true) &&
            $this->payment_method !== 'cod' &&
            $this->payment_status !== 'paid'
        ) {
            throw new InvalidArgumentException('Payment must be marked as paid before this status update.');
        }

        $oldStatus = $currentStatus;
        $oldPaymentStatus = (string) $this->payment_status;
        $this->status = $normalizedStatus;

        // Set timestamp based on status
        match ($normalizedStatus) {
            self::STATUS_PAID => $this->confirmed_at = now(), // Reusing existing column as paid timestamp
            self::STATUS_SHIPPED => $this->shipped_at = now(),
            self::STATUS_DELIVERED => $this->delivered_at = now(),
            self::STATUS_CANCELLED => $this->cancelled_at = now(),
            default => null,
        };

        if ($normalizedStatus === self::STATUS_PAID && $this->payment_status !== 'paid') {
            $this->payment_status = 'paid';
        }

        $this->save();

        // Record status history
        $this->statusHistories()->create([
            'user_id' => $user?->id,
            'old_status' => $oldStatus,
            'new_status' => $normalizedStatus,
            'comment' => $comment,
            'notify_customer' => $notifyCustomer,
        ]);

        // If delivered and COD, mark as paid
        if ($normalizedStatus === self::STATUS_DELIVERED && $this->payment_method === 'cod' && $this->payment_status !== 'paid') {
            $this->update(['payment_status' => 'paid']);
        }

        if (
            $notifyCustomer &&
            in_array($normalizedStatus, [self::STATUS_SHIPPED, self::STATUS_DELIVERED], true)
        ) {
            $orderId = (int) $this->id;

            DB::afterCommit(function () use ($orderId, $normalizedStatus): void {
                $order = self::query()->with('user')->find($orderId);
                if (!$order) {
                    return;
                }

                $notificationService = app(OrderNotificationService::class);

                if ($normalizedStatus === self::STATUS_SHIPPED) {
                    $notificationService->sendOrderShipped($order);
                    return;
                }

                $notificationService->sendOrderDelivered($order);
            });
        }

        app(AuditLogService::class)->log(
            'order.status_changed',
            $this,
            [
                'status' => $oldStatus,
                'payment_status' => $oldPaymentStatus,
            ],
            [
                'status' => (string) $this->status,
                'payment_status' => (string) $this->payment_status,
            ],
            [
                'order_number' => (string) $this->order_number,
                'comment' => $comment,
                'notify_customer' => $notifyCustomer,
                'vendor_id' => (int) ($this->vendor_id ?? 0),
            ],
            $user
        );
    }

    public function canBeCancelled(): bool
    {
        return in_array(
            self::normalizeStatus((string) $this->status),
            [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_PROCESSING],
            true
        );
    }

    public function canRetryOnlinePayment(): bool
    {
        if ((string) $this->payment_method === 'cod') {
            return false;
        }

        $normalizedStatus = self::normalizeStatus((string) $this->status);
        if (in_array($normalizedStatus, [self::STATUS_CANCELLED, self::STATUS_RETURNED], true)) {
            return false;
        }

        if (in_array((string) $this->payment_status, ['paid', 'refunded', 'partially_refunded'], true)) {
            return false;
        }

        $latestPayment = $this->relationLoaded('payments')
            ? $this->payments->sortByDesc('id')->first()
            : $this->payments()->latest('id')->first();

        if (!$latestPayment) {
            return false;
        }

        return !$latestPayment->isCompleted() && (string) $latestPayment->status !== 'refunded';
    }

    public function hasActiveReturnRequest(): bool
    {
        return $this->returnRequests()
            ->whereNotIn('status', [ReturnRequest::STATUS_REJECTED, ReturnRequest::STATUS_REFUNDED])
            ->exists();
    }

    public function canRequestReturn(): bool
    {
        $normalizedStatus = self::normalizeStatus((string) $this->status);

        if ($normalizedStatus !== self::STATUS_DELIVERED) {
            return false;
        }

        return !$this->hasActiveReturnRequest();
    }

    public function cancel(?string $reason = null, ?User $user = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->cancellation_reason = $reason;
        $this->save();

        try {
            $this->updateStatus(self::STATUS_CANCELLED, $user, $reason);
        } catch (InvalidArgumentException) {
            return false;
        }

        // Restore stock
        foreach ($this->items as $item) {
            $item->product->incrementStock($item->quantity);
        }

        return true;
    }

    // Helpers
    public function getStatusBadgeAttribute(): string
    {
        return match (self::normalizeStatus((string) $this->status)) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'info',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_SHIPPED => 'info',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_RETURNED => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel((string) $this->status);
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'partially_refunded' => 'warning',
            default => 'secondary',
        };
    }

    public function getPayoutPayableAmountAttribute(): float
    {
        $payable = (float) $this->total - (float) $this->commission_amount - (float) ($this->refunded_amount ?? 0);
        return round(max(0, $payable), 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return store_money($this->total);
    }

    public function getInvoiceNumberAttribute(): string
    {
        $orderNumber = strtoupper((string) $this->order_number);

        if (str_starts_with($orderNumber, 'ORD-')) {
            $orderNumber = substr($orderNumber, 4);
        }

        return 'INV-' . $orderNumber;
    }

    public function getShippingFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country,
        ]));
    }
}

