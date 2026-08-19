<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return Coupon::where('code', $this->coupon_code)->first();
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items()
            ->get()
            ->sum(fn($item) => $item->total_price);
    }

    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - $this->discount_amount);
    }

    public function addItem(Product $product, int $quantity = 1, ?ProductVariation $variation = null): CartItem
    {
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->where('product_variation_id', $variation?->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            $item = $existingItem->fresh();
            $this->refreshAppliedCoupon();

            return $item;
        }

        $item = $this->items()->create([
            'product_id' => $product->id,
            'product_variation_id' => $variation?->id,
            'quantity' => $quantity,
            'price' => $variation?->final_price ?? $product->final_price,
        ]);

        $this->refreshAppliedCoupon();

        return $item;
    }

    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        $this->refreshAppliedCoupon();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
        $this->refreshAppliedCoupon();
    }

    public function clear(): void
    {
        $this->items()->delete();
        $this->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
    }

    public function applyCoupon(Coupon $coupon): bool
    {
        if (!$coupon->isValid($this, $this->resolveCouponUser())) {
            return false;
        }

        $discount = $coupon->calculateDiscount($this->subtotal);

        $this->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
        ]);

        return true;
    }

    public function removeCoupon(): void
    {
        if ($this->coupon_code === null && (float) $this->discount_amount === 0.0) {
            return;
        }

        $this->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
    }

    public function refreshAppliedCoupon(): void
    {
        if (!$this->coupon_code) {
            return;
        }

        $normalizedCode = strtolower(trim((string) $this->coupon_code));
        if ($normalizedCode === '') {
            $this->removeCoupon();
            return;
        }

        $coupon = Coupon::query()
            ->active()
            ->whereRaw('LOWER(code) = ?', [$normalizedCode])
            ->first();

        if (!$coupon || !$coupon->isValid($this, $this->resolveCouponUser())) {
            $this->removeCoupon();
            return;
        }

        $nextDiscount = $coupon->calculateDiscount($this->subtotal);
        $nextCode = (string) $coupon->code;
        $currentCode = (string) ($this->coupon_code ?? '');
        $currentDiscount = (float) ($this->discount_amount ?? 0);

        if ($currentCode !== $nextCode || abs($currentDiscount - (float) $nextDiscount) > 0.0001) {
            $this->update([
                'coupon_code' => $nextCode,
                'discount_amount' => $nextDiscount,
            ]);
        }
    }

    public static function getOrCreate(?int $userId = null, ?string $sessionId = null): Cart
    {
        if ($userId) {
            $cart = self::firstOrCreate(['user_id' => $userId]);

            // Merge guest cart if exists
            if ($sessionId) {
                $guestCart = self::where('session_id', $sessionId)->whereNull('user_id')->first();
                if ($guestCart) {
                    $shouldCarryCoupon = !$cart->coupon_code && $guestCart->coupon_code;

                    foreach ($guestCart->items as $item) {
                        $cart->addItem($item->product, $item->quantity, $item->variation);
                    }

                    if ($shouldCarryCoupon) {
                        $cart->update([
                            'coupon_code' => $guestCart->coupon_code,
                            'discount_amount' => $guestCart->discount_amount,
                        ]);
                    }

                    $guestCart->delete();
                }
            }

            $cart->refreshAppliedCoupon();

            return $cart;
        }

        return self::firstOrCreate(['session_id' => $sessionId]);
    }

    private function resolveCouponUser(): ?User
    {
        if ($this->relationLoaded('user')) {
            return $this->getRelation('user');
        }

        return $this->user;
    }
}
