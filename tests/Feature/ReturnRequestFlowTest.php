<?php

namespace Tests\Feature;

use App\Models\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class ReturnRequestFlowTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_customer_can_submit_return_request_for_delivered_order(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'total' => 500,
            'subtotal' => 500,
            'commission_amount' => 0,
            'vendor_earning' => 500,
            'delivered_at' => now()->subHour(),
        ]);

        $response = $this
            ->actingAs($customer)
            ->post(route('account.orders.returns.store', $order->order_number), [
                'reason' => 'Damaged item',
                'details' => 'Package came damaged and unusable.',
                'requested_refund_amount' => 250,
            ]);

        $response->assertRedirect(route('account.orders.detail', $order->order_number));
        $response->assertSessionHas('success', 'Return request submitted successfully.');

        $this->assertDatabaseHas('return_requests', [
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'status' => ReturnRequest::STATUS_REQUESTED,
            'reason' => 'Damaged item',
        ]);

        $returnRequest = ReturnRequest::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertSame(250.0, (float) $returnRequest->requested_refund_amount);
        $this->assertDatabaseHas('return_request_status_histories', [
            'return_request_id' => $returnRequest->id,
            'new_status' => ReturnRequest::STATUS_REQUESTED,
        ]);
    }

    public function test_customer_cannot_submit_return_request_for_non_delivered_order(): void
    {
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();

        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'processing',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'total' => 300,
            'subtotal' => 300,
            'commission_amount' => 0,
            'vendor_earning' => 300,
        ]);

        $response = $this
            ->from(route('account.orders.detail', $order->order_number))
            ->actingAs($customer)
            ->post(route('account.orders.returns.store', $order->order_number), [
                'reason' => 'No longer needed',
                'details' => 'I changed my mind.',
            ]);

        $response->assertRedirect(route('account.orders.detail', $order->order_number));
        $response->assertSessionHas('error', 'Return request is not allowed for this order right now.');

        $this->assertDatabaseCount('return_requests', 0);
    }
}
