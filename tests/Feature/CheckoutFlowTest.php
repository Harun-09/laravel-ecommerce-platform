<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRolePermissions();
    }

    public function test_checkout_process_creates_split_cod_orders_for_multi_vendor_cart(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendorA = $this->createApprovedVendor();
        $vendorB = $this->createApprovedVendor();

        $category = $this->createCategory();
        $productA = $this->createProduct($vendorA, $category, ['price' => 120, 'quantity' => 20]);
        $productB = $this->createProduct($vendorB, $category, ['price' => 80, 'quantity' => 15]);

        $this->addItemsToCart($customer, [
            ['product' => $productA, 'quantity' => 1],
            ['product' => $productB, 'quantity' => 2],
        ]);

        $shippingMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ], [
            'cost' => 50,
            'cod_fee' => 20,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.process'), [
            'shipping_name' => 'Customer One',
            'shipping_phone' => '01710000000',
            'shipping_email' => 'customer-one@example.test',
            'shipping_address' => 'House 1, Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'Dhaka',
            'shipping_postal_code' => '1207',
            'shipping_method' => $shippingMethod->id,
            'payment_method' => 'cod',
            'customer_notes' => 'Leave at front desk',
        ]);

        $response->assertRedirectContains('/checkout/success/');
        $response->assertSessionHas('success');

        $orders = Order::query()->where('user_id', $customer->id)->orderBy('id')->get();

        $this->assertCount(2, $orders);
        $this->assertSame(2, $orders->pluck('order_number')->unique()->count());
        $this->assertSame(1, $orders->pluck('checkout_token')->filter()->unique()->count());

        $shippingCosts = $orders->map(fn(Order $order) => (float) $order->shipping_cost)->sort()->values()->all();
        $codFees = $orders->map(fn(Order $order) => (float) $order->cod_fee)->sort()->values()->all();

        $this->assertSame([0.0, 50.0], $shippingCosts);
        $this->assertSame([0.0, 20.0], $codFees);

        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('order_items', 2);
        $this->assertDatabaseCount('order_status_histories', 2);
        $this->assertDatabaseCount('payments', 0);

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertSame(0, $cart->items()->count());
    }

    public function test_checkout_process_requires_create_orders_permission(): void
    {
        Role::findByName('customer')->revokePermissionTo('create orders');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $product = $this->createProduct($vendor, null, ['price' => 99, 'quantity' => 10]);
        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $shippingMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ]);

        $response = $this
            ->from(route('checkout.index'))
            ->actingAs($customer)
            ->post(route('checkout.process'), [
                'shipping_name' => 'Permission Test',
                'shipping_phone' => '01720000000',
                'shipping_address' => 'Dhaka',
                'shipping_city' => 'Dhaka',
                'shipping_method' => $shippingMethod->id,
                'payment_method' => 'cod',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('orders', 0);
    }
}
