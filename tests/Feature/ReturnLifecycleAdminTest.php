<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class ReturnLifecycleAdminTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRolePermissions();
    }

    public function test_admin_can_process_return_lifecycle_until_refunded(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => Order::STATUS_DELIVERED,
            'payment_method' => 'stripe',
            'payment_status' => 'paid',
            'subtotal' => 300,
            'total' => 300,
            'commission_amount' => 0,
            'vendor_earning' => 300,
        ]);

        $returnRequest = $this->createRequestedReturn($order, $customer->id, $vendor->id, 120);

        $approveResponse = $this
            ->from(route('admin.returns.show', $returnRequest))
            ->actingAs($admin)
            ->put(route('admin.returns.update-status', $returnRequest), [
                'status' => ReturnRequest::STATUS_APPROVED,
                'approved_refund_amount' => 120,
                'comment' => 'Approved after product inspection.',
            ]);

        $approveResponse->assertRedirect(route('admin.returns.show', $returnRequest));
        $approveResponse->assertSessionHas('success', 'Return request status updated successfully.');

        $returnRequest->refresh();
        $this->assertSame(ReturnRequest::STATUS_APPROVED, $returnRequest->status);
        $this->assertSame(120.0, (float) $returnRequest->approved_refund_amount);
        $this->assertNotNull($returnRequest->approved_at);

        $pickupResponse = $this
            ->from(route('admin.returns.show', $returnRequest))
            ->actingAs($admin)
            ->put(route('admin.returns.update-status', $returnRequest), [
                'status' => ReturnRequest::STATUS_PICKED_UP,
                'pickup_note' => 'Collected from customer on scheduled pickup window.',
            ]);

        $pickupResponse->assertRedirect(route('admin.returns.show', $returnRequest));
        $pickupResponse->assertSessionHas('success', 'Return request status updated successfully.');

        $returnRequest->refresh();
        $this->assertSame(ReturnRequest::STATUS_PICKED_UP, $returnRequest->status);
        $this->assertSame('Collected from customer on scheduled pickup window.', (string) $returnRequest->pickup_note);
        $this->assertNotNull($returnRequest->picked_up_at);

        $refundResponse = $this
            ->from(route('admin.returns.show', $returnRequest))
            ->actingAs($admin)
            ->put(route('admin.returns.update-status', $returnRequest), [
                'status' => ReturnRequest::STATUS_REFUNDED,
                'approved_refund_amount' => 120,
                'refund_method' => 'stripe',
                'refund_transaction_id' => 'rf_120_test',
                'comment' => 'Partial refund processed successfully.',
            ]);

        $refundResponse->assertRedirect(route('admin.returns.show', $returnRequest));
        $refundResponse->assertSessionHas('success', 'Return request status updated successfully.');

        $returnRequest->refresh();
        $order->refresh();

        $this->assertSame(ReturnRequest::STATUS_REFUNDED, $returnRequest->status);
        $this->assertSame(120.0, (float) $returnRequest->approved_refund_amount);
        $this->assertSame('stripe', (string) $returnRequest->refund_method);
        $this->assertSame('rf_120_test', (string) $returnRequest->refund_transaction_id);
        $this->assertNotNull($returnRequest->refunded_at);
        $this->assertSame((int) $admin->id, (int) $returnRequest->processed_by);

        $this->assertSame(120.0, (float) $order->refunded_amount);
        $this->assertSame('partially_refunded', (string) $order->payment_status);
        $this->assertSame(Order::STATUS_RETURNED, (string) $order->status);
    }

    public function test_admin_cannot_mark_refunded_before_pickup_step(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => Order::STATUS_DELIVERED,
            'payment_method' => 'stripe',
            'payment_status' => 'paid',
            'subtotal' => 260,
            'total' => 260,
            'commission_amount' => 0,
            'vendor_earning' => 260,
        ]);

        $returnRequest = $this->createRequestedReturn($order, $customer->id, $vendor->id, 100);

        $response = $this
            ->from(route('admin.returns.show', $returnRequest))
            ->actingAs($admin)
            ->put(route('admin.returns.update-status', $returnRequest), [
                'status' => ReturnRequest::STATUS_REFUNDED,
                'approved_refund_amount' => 100,
                'refund_method' => 'stripe',
                'refund_transaction_id' => 'rf_invalid_step',
            ]);

        $response->assertRedirect(route('admin.returns.show', $returnRequest));
        $response->assertSessionHas('error', 'Order pickup must be completed before refund.');

        $returnRequest->refresh();
        $order->refresh();

        $this->assertSame(ReturnRequest::STATUS_REQUESTED, (string) $returnRequest->status);
        $this->assertSame(0.0, (float) $order->refunded_amount);
        $this->assertSame('paid', (string) $order->payment_status);
        $this->assertSame(Order::STATUS_DELIVERED, (string) $order->status);
    }

    private function createRequestedReturn(Order $order, int $customerId, int $vendorId, float $requestedAmount): ReturnRequest
    {
        $returnRequest = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $customerId,
            'vendor_id' => $vendorId,
            'status' => ReturnRequest::STATUS_REQUESTED,
            'reason' => 'Damaged packaging',
            'details' => 'Package was damaged and product has visible marks.',
            'requested_refund_amount' => $requestedAmount,
        ]);

        $returnRequest->statusHistories()->create([
            'user_id' => $customerId,
            'old_status' => null,
            'new_status' => ReturnRequest::STATUS_REQUESTED,
            'comment' => 'Customer submitted return request.',
            'notify_customer' => true,
        ]);

        return $returnRequest;
    }
}

