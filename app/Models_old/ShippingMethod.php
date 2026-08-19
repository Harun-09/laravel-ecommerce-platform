<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'name',
        'description',
        'type',
        'cost',
        'cod_fee',
        'minimum_order_amount',
        'per_kg_cost',
        'estimated_days',
        'is_cod_available',
        'is_active',
        'order',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'cod_fee' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'per_kg_cost' => 'decimal:2',
        'is_cod_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function calculateCost(Cart $cart): float
    {
        return $this->calculateQuote($cart)['shipping_cost'];
    }

    public function calculateQuote(Cart $cart, bool $includeCodFee = false): array
    {
        $shippingCost = 0.0;

        if ($this->type === 'free') {
            if ($this->minimum_order_amount && $cart->subtotal < $this->minimum_order_amount) {
                $shippingCost = (float) $this->cost;
            } else {
                $shippingCost = 0.0;
            }
        }

        if ($this->type === 'weight_based') {
            $totalWeight = $cart->items->sum(
                fn($item) =>
                ($item->product->weight ?? 0) * $item->quantity
            );
            $shippingCost = (float) $this->cost + ($totalWeight * (float) ($this->per_kg_cost ?? 0));
        }

        if ($this->type === 'price_based') {
            // Implement tier-based pricing if needed
            $shippingCost = (float) $this->cost;
        }

        if ($this->type === 'flat') {
            $shippingCost = (float) $this->cost;
        }

        $shippingDiscount = 0.0;
        $appliedCouponCode = null;
        $appliedCouponType = null;
        $isFreeShippingApplied = false;
        $coupon = $cart->coupon();
        if ($coupon && $coupon->isFreeShippingType() && $coupon->isValid($cart, $cart->user)) {
            $shippingDiscount = $coupon->calculateShippingDiscount($shippingCost);
            $shippingCost = max(0, $shippingCost - $shippingDiscount);
            $appliedCouponCode = (string) $coupon->code;
            $appliedCouponType = (string) $coupon->normalizedType();
            $isFreeShippingApplied = $shippingDiscount > 0;
        }

        $codFee = ($includeCodFee && $this->is_cod_available) ? (float) $this->cod_fee : 0.0;

        return [
            'shipping_cost' => round($shippingCost, 2),
            'shipping_discount' => round($shippingDiscount, 2),
            'cod_fee' => round($codFee, 2),
            'total_shipping_cost' => round($shippingCost + $codFee, 2),
            'applied_coupon_code' => $appliedCouponCode,
            'applied_coupon_type' => $appliedCouponType,
            'is_free_shipping_applied' => $isFreeShippingApplied,
        ];
    }
}
