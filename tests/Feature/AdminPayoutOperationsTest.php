<?php

namespace Tests\Feature;

use App\Models\VendorPayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class AdminPayoutOperationsTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_admin_can_create_vendor_payout_batch_from_eligible_orders(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $orderA = $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'subtotal' => 1000,
            'total' => 1000,
            'commission_amount' => 100,
            'vendor_earning' => 900,
            'refunded_amount' => 0,
        ]);

        $orderB = $this->createOrderForUser($customer, $vendor, [
            'status' => 'returned',
            'payment_status' => 'partially_refunded',
            'payment_method' => 'stripe',
            'subtotal' => 700,
            'total' => 700,
            'commission_amount' => 70,
            'vendor_earning' => 630,
            'refunded_amount' => 50,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.payouts.store'), [
                'vendor_id' => $vendor->id,
                'payment_method' => 'bank_transfer',
                'payment_details' => 'AC 12345',
                'notes' => 'Weekly vendor settlement',
            ]);

        $payout = VendorPayout::query()->latest('id')->first();

        $response->assertRedirect(route('admin.payouts.show', $payout));
        $response->assertSessionHas('success');

        $this->assertNotNull($payout);
        $this->assertSame((int) $vendor->id, (int) $payout->vendor_id);
        $this->assertSame('pending', $payout->status);
        $this->assertSame(1700.0, (float) $payout->amount);
        $this->assertSame(170.0, (float) $payout->platform_fee);
        $this->assertSame(1480.0, (float) $payout->net_amount);

        $this->assertDatabaseCount('vendor_payout_items', 2);
        $this->assertTrue($orderA->fresh()->payoutItems()->exists());
        $this->assertTrue($orderB->fresh()->payoutItems()->exists());
    }

    public function test_admin_can_process_pending_payout(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'subtotal' => 1200,
            'total' => 1200,
            'commission_amount' => 120,
            'vendor_earning' => 1080,
            'refunded_amount' => 0,
        ]);

        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => 1200,
            'platform_fee' => 120,
            'net_amount' => 1080,
            'payment_method' => 'bank_transfer',
            'payment_details' => null,
            'status' => 'pending',
            'notes' => null,
        ]);

        $payout->items()->create([
            'order_id' => $order->id,
            'order_total' => 1200,
            'commission_amount' => 120,
            'refund_amount' => 0,
            'vendor_earning' => 1080,
            'payable_amount' => 1080,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.payouts.process', $payout), [
                'reference_number' => 'TXN-SETTLED-001',
                'payment_details' => 'Bank transfer by admin',
                'notes' => 'Settled successfully',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Payout marked as completed successfully.');

        $payout->refresh();
        $this->assertSame('completed', $payout->status);
        $this->assertSame('TXN-SETTLED-001', $payout->reference_number);
        $this->assertSame('Bank transfer by admin', $payout->payment_details);
        $this->assertSame('Settled successfully', $payout->notes);
        $this->assertSame((int) $admin->id, (int) $payout->processed_by);
        $this->assertNotNull($payout->processed_at);

        $item = $payout->items()->first();
        $this->assertNotNull($item);
        $item = $item->fresh();
        $this->assertNotNull($item);
        $this->assertNotNull($item->posted_at);
        $this->assertSame((int) $admin->id, (int) $item->posted_by);
    }

    public function test_admin_cannot_create_payout_below_minimum_threshold(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'subtotal' => 200,
            'total' => 200,
            'commission_amount' => 20,
            'vendor_earning' => 180,
            'refunded_amount' => 0,
        ]);

        $response = $this
            ->from(route('admin.payouts.index'))
            ->actingAs($admin)
            ->post(route('admin.payouts.store'), [
                'vendor_id' => $vendor->id,
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertRedirect(route('admin.payouts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('vendor_payouts', 0);
    }

    public function test_payout_routes_require_specific_permissions(): void
    {
        Role::findByName('admin')->revokePermissionTo('view payouts');
        Role::findByName('admin')->revokePermissionTo('create payouts');
        Role::findByName('admin')->revokePermissionTo('process payouts');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'total' => 900,
            'subtotal' => 900,
            'commission_amount' => 90,
            'vendor_earning' => 810,
            'refunded_amount' => 0,
        ]);

        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => 900,
            'platform_fee' => 90,
            'net_amount' => 810,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $payout->items()->create([
            'order_id' => $order->id,
            'order_total' => 900,
            'commission_amount' => 90,
            'refund_amount' => 0,
            'vendor_earning' => 810,
            'payable_amount' => 810,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payouts.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.payouts.store'), [
                'vendor_id' => $vendor->id,
                'payment_method' => 'bank_transfer',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('admin.payouts.process', $payout), [
                'reference_number' => 'TXN-101',
            ])
            ->assertForbidden();
    }
}
