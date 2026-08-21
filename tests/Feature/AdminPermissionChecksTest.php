<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Models\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class AdminPermissionChecksTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_admin_without_process_refunds_permission_cannot_access_return_management_routes(): void
    {
        Role::findByName('admin')->revokePermissionTo('process refunds');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'subtotal' => 200,
            'total' => 200,
            'commission_amount' => 0,
            'vendor_earning' => 200,
        ]);

        $returnRequest = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'status' => ReturnRequest::STATUS_REQUESTED,
            'reason' => 'Wrong size delivered',
            'requested_refund_amount' => 80,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.returns.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.returns.update-status', $returnRequest), [
                'status' => ReturnRequest::STATUS_APPROVED,
                'approved_refund_amount' => 80,
            ])
            ->assertForbidden();
    }

    public function test_admin_without_view_orders_permission_cannot_access_admin_orders_index(): void
    {
        Role::findByName('admin')->revokePermissionTo('view orders');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }
}

