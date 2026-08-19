<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class CartCouponFlowTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_customer_can_add_update_and_remove_cart_item_via_json_endpoints(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 250,
            'quantity' => 20,
        ]);

        $addResponse = $this
            ->actingAs($customer)
            ->postJson(route('cart.add'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $addResponse->assertOk();
        $addResponse->assertJson([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => 2,
        ]);

        $cart = Cart::query()->where('user_id', $customer->id)->with('items')->firstOrFail();
        $item = $cart->items->firstOrFail();

        $updateResponse = $this
            ->actingAs($customer)
            ->postJson(route('cart.update'), [
                'item_id' => $item->id,
                'quantity' => 4,
            ]);

        $updateResponse->assertOk();
        $updateResponse->assertJson([
            'success' => true,
            'message' => 'Cart updated',
        ]);

        $item->refresh();
        $this->assertSame(4, (int) $item->quantity);

        $removeResponse = $this
            ->actingAs($customer)
            ->postJson(route('cart.remove'), [
                'item_id' => $item->id,
            ]);

        $removeResponse->assertOk();
        $removeResponse->assertJson([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_can_apply_and_remove_coupon_from_cart(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 400,
            'quantity' => 15,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $coupon = $this->createCoupon([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
        ]);

        $applyResponse = $this
            ->actingAs($customer)
            ->postJson(route('cart.apply-coupon'), [
                'coupon_code' => $coupon->code,
            ]);

        $applyResponse->assertOk();
        $applyResponse->assertJson([
            'success' => true,
        ]);

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertSame('SAVE10', (string) $cart->coupon_code);
        $this->assertGreaterThan(0, (float) $cart->discount_amount);

        $removeResponse = $this
            ->actingAs($customer)
            ->postJson(route('cart.remove-coupon'));

        $removeResponse->assertOk();
        $removeResponse->assertJson([
            'success' => true,
            'message' => 'Coupon removed',
        ]);

        $cart->refresh();
        $this->assertNull($cart->coupon_code);
        $this->assertSame(0.0, (float) $cart->discount_amount);
    }

    public function test_apply_coupon_returns_error_for_invalid_code(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 120,
            'quantity' => 12,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $response = $this
            ->actingAs($customer)
            ->postJson(route('cart.apply-coupon'), [
                'coupon_code' => 'INVALID-CODE',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid or expired coupon code',
        ]);
    }

    private function createCoupon(array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'CPN-' . Str::upper(Str::random(6)),
            'name' => 'Test Coupon',
            'description' => 'Test coupon for cart flow.',
            'type' => 'percentage',
            'value' => 5,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(5),
        ], $attributes));
    }
}

