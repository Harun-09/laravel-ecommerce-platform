<?php

namespace Tests\Feature\Concerns;

use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Payment;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Review;
use App\Domains\ECommerce\Models\ShippingMethod;
use App\Domains\ECommerce\Models\ShippingZone;
use App\Models\User;
use App\Domains\ECommerce\Models\Vendor;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

trait BuildsEcommerceData
{
    protected function seedRolePermissions(): void
    {
        $this->seed(RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function createUserWithRole(?string $role = null, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    protected function createApprovedVendor(?User $user = null, array $attributes = []): Vendor
    {
        $user ??= $this->createUserWithRole('vendor');

        if (!$user->hasRole('vendor')) {
            $user->assignRole('vendor');
        }

        $slugSuffix = Str::lower(Str::random(8));

        return Vendor::create(array_merge([
            'user_id' => $user->id,
            'shop_name' => 'Shop ' . Str::upper(Str::random(4)),
            'slug' => 'shop-' . $slugSuffix,
            'phone' => '01700000000',
            'email' => 'vendor-' . $slugSuffix . '@example.test',
            'address' => 'Dhaka, Bangladesh',
            'commission_type' => 'percentage',
            'commission_rate' => 10,
            'status' => 'approved',
            'approved_at' => now(),
        ], $attributes));
    }

    protected function createCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Category ' . Str::upper(Str::random(4)),
            'slug' => 'category-' . Str::lower(Str::random(8)),
            'is_active' => true,
        ], $attributes));
    }

    protected function createProduct(Vendor $vendor, ?Category $category = null, array $attributes = []): Product
    {
        $category ??= $this->createCategory();

        return Product::create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Product ' . Str::upper(Str::random(4)),
            'slug' => 'product-' . Str::lower(Str::random(10)),
            'price' => 100,
            'quantity' => 50,
            'status' => 'active',
            'track_quantity' => true,
            'allow_backorder' => false,
        ], $attributes));
    }

    protected function createShippingMethodForCity(
        string $city = 'Dhaka',
        array $zoneAttributes = [],
        array $methodAttributes = []
    ): ShippingMethod {
        $zone = ShippingZone::create(array_merge([
            'name' => 'Zone ' . $city,
            'code' => Str::slug($city, '_'),
            'regions' => [$city],
            'is_active' => true,
            'order' => 1,
        ], $zoneAttributes));

        return ShippingMethod::create(array_merge([
            'shipping_zone_id' => $zone->id,
            'name' => 'Standard Delivery',
            'type' => 'flat',
            'cost' => 50,
            'cod_fee' => 20,
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 1,
        ], $methodAttributes));
    }

    /**
     * @param  array<int, array{product: Product, quantity?: int}>  $lineItems
     */
    protected function addItemsToCart(User $user, array $lineItems): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($lineItems as $item) {
            $cart->addItem($item['product'], (int) ($item['quantity'] ?? 1));
        }

        return $cart->fresh('items.product');
    }

    protected function createOrderForUser(User $user, Vendor $vendor, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'checkout_token' => (string) Str::uuid(),
            'status' => 'processing',
            'payment_status' => 'pending',
            'subtotal' => 100,
            'discount_amount' => 0,
            'shipping_cost' => 0,
            'cod_fee' => 0,
            'tax_amount' => 0,
            'total' => 100,
            'refunded_amount' => 0,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'vendor_earning' => 90,
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01700000000',
            'shipping_email' => 'customer@example.test',
            'shipping_address' => 'Road 1, Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_country' => 'Bangladesh',
            'payment_method' => 'stripe',
        ], $attributes));
    }

    protected function createPaymentForOrder(Order $order, array $attributes = []): Payment
    {
        return Payment::create(array_merge([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_method' => (string) $order->payment_method,
            'amount' => (float) $order->total,
            'currency' => 'BDT',
            'status' => 'pending',
        ], $attributes));
    }

    protected function createOrderItem(Order $order, Product $product, int $quantity = 1, ?float $unitPrice = null): OrderItem
    {
        $price = $unitPrice ?? (float) $product->price;

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variation_id' => null,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'variation_details' => null,
            'product_image' => null,
            'quantity' => $quantity,
            'unit_price' => $price,
            'total_price' => $price * $quantity,
        ]);
    }

    protected function createReview(User $user, Product $product, ?Order $order = null, array $attributes = []): Review
    {
        return Review::create(array_merge([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order?->id,
            'rating' => 4,
            'title' => 'Good product',
            'comment' => 'Very useful product and quality is decent for daily use.',
            'is_verified_purchase' => $order !== null,
            'is_approved' => false,
        ], $attributes));
    }
}
