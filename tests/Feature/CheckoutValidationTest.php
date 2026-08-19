<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class CheckoutValidationTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_checkout_process_validates_required_fields(): void
    {
        $customer = $this->createUserWithRole('customer');

        $response = $this
            ->from(route('checkout.index'))
            ->actingAs($customer)
            ->post(route('checkout.process'), []);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHasErrors([
            'shipping_name',
            'shipping_phone',
            'shipping_address',
            'shipping_city',
            'shipping_method',
            'payment_method',
        ]);
    }

    public function test_checkout_rejects_shipping_method_outside_selected_delivery_zone(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 180,
            'quantity' => 20,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $dhakaMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ]);

        $this->createShippingMethodForCity('Sylhet', [
            'code' => 'inside_sylhet',
            'regions' => ['Sylhet'],
        ]);

        $response = $this
            ->from(route('checkout.index'))
            ->actingAs($customer)
            ->post(route('checkout.process'), [
                'shipping_name' => 'Zone Test Customer',
                'shipping_phone' => '01710000000',
                'shipping_email' => 'zone-test@example.test',
                'shipping_address' => 'Test Address',
                'shipping_city' => 'Sylhet',
                'shipping_state' => 'Sylhet',
                'shipping_postal_code' => '3100',
                'shipping_method' => $dhakaMethod->id,
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHas('error', 'Selected shipping method is not available for your delivery zone.');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_cod_for_method_where_cod_is_disabled(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor, null, [
            'price' => 320,
            'quantity' => 20,
        ]);

        $this->addItemsToCart($customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $nonCodMethod = $this->createShippingMethodForCity('Dhaka', [
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka'],
        ], [
            'is_cod_available' => false,
        ]);

        $response = $this
            ->from(route('checkout.index'))
            ->actingAs($customer)
            ->post(route('checkout.process'), [
                'shipping_name' => 'COD Restriction Customer',
                'shipping_phone' => '01710000001',
                'shipping_email' => 'cod-restriction@example.test',
                'shipping_address' => 'Dhaka Address',
                'shipping_city' => 'Dhaka',
                'shipping_state' => 'Dhaka',
                'shipping_postal_code' => '1207',
                'shipping_method' => $nonCodMethod->id,
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHas('error', 'Cash on delivery is not available for this shipping method.');
        $this->assertDatabaseCount('orders', 0);
    }
}
