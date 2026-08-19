<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_non_customer_account_is_redirected_from_checkout_flow(): void
    {
        $vendorUser = $this->createUserWithRole('vendor');

        $response = $this->actingAs($vendorUser)->get(route('checkout.index'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('warning', 'Checkout and payment are available for customer accounts only.');
    }

    public function test_user_without_view_own_orders_permission_gets_forbidden(): void
    {
        $user = $this->createUserWithRole(null);

        $response = $this->actingAs($user)->get(route('account.orders'));

        $response->assertForbidden();
    }

    public function test_admin_account_is_redirected_away_from_customer_account_routes(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('account.orders'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('warning', 'Admin accounts are redirected to the admin dashboard.');
    }
}
