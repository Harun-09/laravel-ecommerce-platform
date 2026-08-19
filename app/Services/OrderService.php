<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Payment;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Coupon;
use App\Services\OrderNotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createOrder(Cart $cart, array $data): Order
    {
        return $this->createOrders($cart, $data)->firstOrFail();
    }

    public function createOrders(Cart $cart, array $data): Collection
    {
        return DB::transaction(function () use ($cart, $data) {
            // Group cart items by vendor
            $itemsByVendor = $cart->items->groupBy(fn($item) => $item->product->vendor_id);
            $baseShippingCost = (float) ($data['shipping_cost'] ?? 0);
            $baseCodFee = (float) ($data['cod_fee'] ?? 0);
            $checkoutToken = (string) ($data['checkout_token'] ?? Str::uuid());

            $orders = [];
            $orderIds = [];

            foreach ($itemsByVendor as $vendorId => $items) {
                $subtotal = $items->sum(fn($item) => $item->total_price);
                $vendor = $items->first()->product->vendor;
                $shippingCost = count($orders) === 0 ? $baseShippingCost : 0;
                $codFee = count($orders) === 0 ? $baseCodFee : 0;
                $allocatedDiscount = 0.0;

                if ((float) $cart->subtotal > 0) {
                    $allocatedDiscount = ((float) ($cart->discount_amount ?? 0) / (float) $cart->subtotal) * $subtotal;
                }

                // Calculate commission
                $commissionData = $vendor->calculateEarning($subtotal);

                $order = Order::create([
                    'user_id' => $cart->user_id,
                    'vendor_id' => $vendorId,
                    'checkout_token' => $checkoutToken,
                    'coupon_id' => $cart->coupon()?->id,
                    'status' => Order::STATUS_PENDING,
                    'payment_status' => 'pending',
                    'subtotal' => $subtotal,
                    'discount_amount' => $allocatedDiscount,
                    'shipping_cost' => $shippingCost,
                    'cod_fee' => $codFee,
                    'tax_amount' => 0,
                    'total' => $subtotal - $allocatedDiscount + $shippingCost + $codFee,
                    'refunded_amount' => 0,
                    'commission_rate' => $vendor->commission_rate,
                    'commission_amount' => $commissionData['commission'],
                    'vendor_earning' => $commissionData['earning'],
                    'shipping_name' => $data['shipping_name'],
                    'shipping_phone' => $data['shipping_phone'],
                    'shipping_email' => $data['shipping_email'] ?? null,
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city' => $data['shipping_city'],
                    'delivery_zone' => $data['delivery_zone'] ?? null,
                    'shipping_state' => $data['shipping_state'] ?? null,
                    'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                    'shipping_country' => $data['shipping_country'] ?? 'Bangladesh',
                    'shipping_method' => $data['shipping_method'] ?? null,
                    'payment_method' => $data['payment_method'],
                    'customer_notes' => $data['customer_notes'] ?? null,
                ]);

                $order->statusHistories()->create([
                    'user_id' => $cart->user_id,
                    'old_status' => null,
                    'new_status' => Order::STATUS_PENDING,
                    'comment' => 'Order placed successfully.',
                    'notify_customer' => true,
                ]);

                // Create order items
                foreach ($items as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_variation_id' => $cartItem->product_variation_id,
                        'product_name' => $cartItem->product->name,
                        'product_sku' => $cartItem->product->sku,
                        'variation_details' => $cartItem->variation?->attributeValues->map(fn($v) => [
                            'attribute' => $v->attribute->name,
                            'value' => $v->value,
                        ])->toArray(),
                        'product_image' => $cartItem->product->primaryImage?->image,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->price,
                        'total_price' => $cartItem->total_price,
                    ]);

                    // Decrement stock
                    $cartItem->product->decrementStock($cartItem->quantity);
                }

                // Create payment record
                if ($data['payment_method'] !== 'cod') {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $cart->user_id,
                        'payment_method' => $data['payment_method'],
                        'amount' => $order->total,
                        'status' => 'pending',
                    ]);
                }

                // Mark coupon as used
                if ($cart->coupon_code) {
                    $coupon = Coupon::where('code', $cart->coupon_code)->first();
                    if ($coupon && $cart->user_id) {
                        $coupon->markAsUsed($cart->user, $order);
                    }
                }

                $orders[] = $order;
                $orderIds[] = $order->id;
            }

            // Clear cart
            $cart->clear();

            DB::afterCommit(function () use ($orderIds): void {
                if (empty($orderIds)) {
                    return;
                }

                $notificationService = app(OrderNotificationService::class);

                Order::query()
                    ->whereIn('id', $orderIds)
                    ->with('user')
                    ->get()
                    ->each(fn(Order $order) => $notificationService->sendOrderPlaced($order));
            });

            return collect($orders);
        });
    }

    public function updateOrderStatus(Order $order, string $status, ?string $comment = null): void
    {
        $order->updateStatus($status, auth()->user(), $comment, true);
    }

    public function cancelOrder(Order $order, ?string $reason = null): bool
    {
        return $order->cancel($reason, auth()->user());
    }

    public function getOrderStats(int $vendorId = null): array
    {
        $query = Order::query();

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        } elseif (auth()->check() && auth()->user()->hasRole('vendor')) {
            $query->forCurrentVendor(auth()->user());
        }

        return [
            'pending' => (clone $query)->pending()->count(),
            'paid' => (clone $query)->paidStatus()->count(),
            'processing' => (clone $query)->processing()->count(),
            'shipped' => (clone $query)->shipped()->count(),
            'delivered' => (clone $query)->delivered()->count(),
            'cancelled' => (clone $query)->cancelled()->count(),
            'returned' => (clone $query)->returned()->count(),
            'total_revenue' => (clone $query)->paid()->sum('total'),
        ];
    }
}
