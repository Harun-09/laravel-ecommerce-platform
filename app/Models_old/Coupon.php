<?php

namespace App\Models;

use App\Domains\ECommerce\Models\Concerns\EnforcesVendorIsolation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, EnforcesVendorIsolation;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';
    public const TYPE_FREE_SHIPPING = 'free_shipping';

    protected $fillable = [
        'vendor_id',
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function isValid(Cart $cart, ?User $user = null): bool
    {
        $vendorIds = $cart->items()
            ->with('product:id,vendor_id')
            ->get()
            ->pluck('product.vendor_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        // Vendor-specific coupons are restricted to a single matching vendor cart
        if ($this->vendor_id) {
            if ($vendorIds->count() !== 1) {
                return false;
            }

            if ((int) $vendorIds->first() !== (int) $this->vendor_id) {
                return false;
            }
        }

        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        // Check minimum order amount
        if ($this->minimum_order_amount && $cart->subtotal < $this->minimum_order_amount) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        // Check per-user usage limit
        if ($user && $this->usage_limit_per_user) {
            $userUsages = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsages >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        $subtotal = max(0, (float) $subtotal);

        $discount = match ($this->normalizedType()) {
            self::TYPE_PERCENTAGE => ($subtotal * (float) $this->value) / 100,
            self::TYPE_FIXED => min((float) $this->value, $subtotal),
            self::TYPE_FREE_SHIPPING => 0.0,
            default => 0.0,
        };

        if ($this->normalizedType() === self::TYPE_PERCENTAGE && $this->maximum_discount) {
            $discount = min($discount, (float) $this->maximum_discount);
        }

        return round(max(0, (float) $discount), 2);
    }

    public function isFreeShippingType(): bool
    {
        return $this->normalizedType() === self::TYPE_FREE_SHIPPING;
    }

    public function calculateShippingDiscount(float $shippingCost): float
    {
        if (!$this->isFreeShippingType()) {
            return 0.0;
        }

        $shippingCost = max(0, $shippingCost);
        if ($shippingCost <= 0) {
            return 0.0;
        }

        $discount = $shippingCost;

        // For compatibility, if value is set on free-shipping coupons, treat it as a discount cap.
        if ((float) $this->value > 0) {
            $discount = min($discount, (float) $this->value);
        }

        if ($this->maximum_discount) {
            $discount = min($discount, (float) $this->maximum_discount);
        }

        return round(max(0, $discount), 2);
    }

    public function normalizedType(): string
    {
        $type = strtolower(trim((string) $this->type));

        return in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_FIXED, self::TYPE_FREE_SHIPPING], true)
            ? $type
            : self::TYPE_PERCENTAGE;
    }

    public function markAsUsed(User $user, Order $order): void
    {
        $this->usages()->create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $order->discount_amount,
        ]);
        $this->increment('used_count');
    }
}
