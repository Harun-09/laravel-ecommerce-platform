<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Coupon;
use App\Domains\ECommerce\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class CouponTypeCoverageTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRolePermissions();
    }

    public function test_free_shipping_coupon_waives_shipping_cost_but_keeps_cod_fee(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 1200,
            'quantity' => 20,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();

        $coupon = $this->createCoupon([
            'code' => 'FREESHIP-' . Str::upper(Str::random(6)),
            'name' => 'Free Shipping Promo',
            'type' => 'free_shipping',
            'value' => 0,
            'minimum_order_amount' => 1000,
            'maximum_discount' => null,
        ]);

        $this->assertTrue($cart->applyCoupon($coupon));
        $this->assertSame(0.0, (float) $cart->fresh()->discount_amount);

        $shippingMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ], [
            'type' => 'flat',
            'cost' => 80,
            'cod_fee' => 20,
            'is_cod_available' => true,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.process'), [
            'shipping_name' => 'Free Shipping Customer',
            'shipping_phone' => '01710000000',
            'shipping_email' => 'free-shipping@example.test',
            'shipping_address' => 'Road 1, Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'Dhaka',
            'shipping_postal_code' => '1207',
            'shipping_method' => $shippingMethod->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirectContains('/checkout/success/');

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertSame((int) $coupon->id, (int) $order->coupon_id);
        $this->assertSame(0.0, (float) $order->discount_amount);
        $this->assertSame(0.0, (float) $order->shipping_cost);
        $this->assertSame(20.0, (float) $order->cod_fee);
        $this->assertSame(1220.0, (float) $order->total);
    }

    public function test_percentage_coupon_does_not_override_shipping_cost(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 1000,
            'quantity' => 20,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();

        $coupon = $this->createCoupon([
            'code' => 'PCT10-' . Str::upper(Str::random(6)),
            'name' => 'Ten Percent',
            'type' => 'percentage',
            'value' => 10,
            'minimum_order_amount' => 500,
            'maximum_discount' => null,
        ]);

        $this->assertTrue($cart->applyCoupon($coupon));
        $this->assertSame(100.0, (float) $cart->fresh()->discount_amount);

        $shippingMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ], [
            'type' => 'flat',
            'cost' => 50,
            'cod_fee' => 20,
            'is_cod_available' => true,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.process'), [
            'shipping_name' => 'Percentage Coupon Customer',
            'shipping_phone' => '01710000001',
            'shipping_email' => 'percentage@example.test',
            'shipping_address' => 'Road 2, Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'Dhaka',
            'shipping_postal_code' => '1208',
            'shipping_method' => $shippingMethod->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirectContains('/checkout/success/');

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertSame((int) $coupon->id, (int) $order->coupon_id);
        $this->assertSame(100.0, (float) $order->discount_amount);
        $this->assertSame(50.0, (float) $order->shipping_cost);
        $this->assertSame(20.0, (float) $order->cod_fee);
        $this->assertSame(970.0, (float) $order->total);
    }

    public function test_shipping_quote_endpoint_applies_free_shipping_coupon_hook(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 800,
            'quantity' => 20,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();

        $coupon = $this->createCoupon([
            'code' => 'FREESHIPAPI-' . Str::upper(Str::random(4)),
            'name' => 'Free Shipping API',
            'type' => 'free_shipping',
            'value' => 0,
            'minimum_order_amount' => 500,
            'maximum_discount' => null,
        ]);

        $this->assertTrue($cart->applyCoupon($coupon));

        $shippingMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ], [
            'type' => 'flat',
            'cost' => 70,
            'cod_fee' => 15,
            'is_cod_available' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('checkout.shipping-methods', [
            'city' => 'Dhaka',
            'payment_method' => 'cod',
        ]));

        $response->assertOk()
            ->assertJsonPath('coupon.code', $coupon->code)
            ->assertJsonPath('coupon.type', 'free_shipping')
            ->assertJsonPath('coupon.is_free_shipping', true)
            ->assertJsonPath('methods.0.id', $shippingMethod->id)
            ->assertJsonPath('methods.0.shipping_cost', 0)
            ->assertJsonPath('methods.0.shipping_discount', 70)
            ->assertJsonPath('methods.0.cod_fee', 15)
            ->assertJsonPath('methods.0.total_cost', 15)
            ->assertJsonPath('methods.0.applied_coupon_code', $coupon->code)
            ->assertJsonPath('methods.0.applied_coupon_type', 'free_shipping')
            ->assertJsonPath('methods.0.is_free_shipping_applied', true);
    }

    private function createCoupon(array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'vendor_id' => null,
            'code' => 'TEST-' . Str::upper(Str::random(8)),
            'name' => 'Test Coupon',
            'description' => 'Test coupon',
            'type' => 'percentage',
            'value' => 5,
            'minimum_order_amount' => 0,
            'maximum_discount' => null,
            'usage_limit' => null,
            'usage_limit_per_user' => null,
            'used_count' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ], $attributes));
    }
}
