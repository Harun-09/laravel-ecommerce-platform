<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\ProductVariation;
use App\Domains\ECommerce\Models\Coupon;

class CartService
{
    protected ?Cart $cart = null;

    public function getCart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        $userId = auth()->id();
        $sessionId = session()->getId();

        $this->cart = Cart::getOrCreate($userId, $sessionId);
        if (method_exists($this->cart, 'refreshAppliedCoupon')) {
            $this->cart->refreshAppliedCoupon();
        }

        return $this->cart;
    }

    public function addToCart(Product $product, int $quantity = 1, ?int $variationId = null): array
    {
        $cart = $this->getCart();

        // Validate stock
        if ($product->track_quantity) {
            $availableQty = $variationId
                ? ProductVariation::find($variationId)?->quantity ?? 0
                : $product->availableStock();

            if ($quantity > $availableQty && !$product->allow_backorder) {
                return [
                    'success' => false,
                    'message' => 'Requested quantity not available',
                ];
            }
        }

        $variation = $variationId ? ProductVariation::find($variationId) : null;
        $cart->addItem($product, $quantity, $variation);

        return [
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cart->fresh()->total_items,
        ];
    }

    public function updateQuantity(int $itemId, int $quantity): array
    {
        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);

        if (!$item) {
            return [
                'success' => false,
                'message' => 'Item not found in cart',
            ];
        }

        if ($quantity <= 0) {
            $item->delete();
            return [
                'success' => true,
                'message' => 'Item removed from cart',
                'cart' => $this->getCartData(),
            ];
        }

        $item->update(['quantity' => $quantity]);

        return [
            'success' => true,
            'message' => 'Cart updated',
            'cart' => $this->getCartData(),
        ];
    }

    public function removeItem(int $itemId): array
    {
        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);

        if ($item) {
            $item->delete();
        }

        return [
            'success' => true,
            'message' => 'Item removed from cart',
            'cart' => $this->getCartData(),
        ];
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->getCart();
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code',
            ];
        }

        if (!$coupon->isValid($cart, auth()->user())) {
            return [
                'success' => false,
                'message' => 'This coupon cannot be applied to your order',
            ];
        }

        if ($cart->applyCoupon($coupon)) {
            return [
                'success' => true,
                'message' => 'Coupon applied successfully',
                'discount' => $cart->fresh()->discount_amount,
                'cart' => $this->getCartData(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to apply coupon',
        ];
    }

    public function removeCoupon(): array
    {
        $cart = $this->getCart();
        $cart->removeCoupon();

        return [
            'success' => true,
            'message' => 'Coupon removed',
            'cart' => $this->getCartData(),
        ];
    }

    public function clear(): array
    {
        $cart = $this->getCart();
        $cart->clear();

        return [
            'success' => true,
            'message' => 'Cart cleared',
        ];
    }

    public function getCartData(): array
    {
        $cart = $this->getCart()->load('items.product.primaryImage', 'items.variation');

        return [
            'items' => $cart->items->map(fn($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_image' => $item->product_image,
                'variation' => $item->variation?->variation_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->total_price,
                'in_stock' => $item->isInStock(),
            ]),
            'subtotal' => $cart->subtotal,
            'discount' => $cart->discount_amount,
            'coupon_code' => $cart->coupon_code,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ];
    }
}
