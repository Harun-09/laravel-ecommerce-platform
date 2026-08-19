<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\StripeGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mockery;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class StripePaymentFlowTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_payment_process_updates_all_split_payments_and_redirects_to_stripe(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'checkoutToken' => $checkoutToken,
            'orderA' => $orderA,
            'orderB' => $orderB,
            'paymentA' => $paymentA,
            'paymentB' => $paymentB,
        ] = $this->createSplitStripeCheckout($customer);

        $stripeMock = Mockery::mock(StripeGatewayService::class);
        $stripeMock->shouldReceive('isConfigured')->once()->andReturn(true);
        $stripeMock->shouldReceive('createCheckoutSessionForOrders')
            ->once()
            ->withArgs(function (Order $referenceOrder, Collection $orders, Collection $payments) use ($orderA, $orderB, $paymentA, $paymentB, $checkoutToken): bool {
                return $referenceOrder->is($orderA)
                    && $orders->pluck('id')->sort()->values()->all() === [$orderA->id, $orderB->id]
                    && $payments->pluck('id')->sort()->values()->all() === [$paymentA->id, $paymentB->id]
                    && $orders->every(fn(Order $order) => (string) $order->checkout_token === $checkoutToken);
            })
            ->andReturn([
                'id' => 'cs_test_batch_1',
                'url' => 'https://stripe.test/checkout/cs_test_batch_1',
            ]);

        $this->app->instance(StripeGatewayService::class, $stripeMock);

        $response = $this
            ->actingAs($customer)
            ->get(route('payment.process', $orderA->order_number));

        $response->assertRedirect('https://stripe.test/checkout/cs_test_batch_1');

        $paymentA->refresh();
        $paymentB->refresh();
        $orderA->refresh();
        $orderB->refresh();

        $this->assertSame('processing', $paymentA->status);
        $this->assertSame('processing', $paymentB->status);
        $this->assertSame('cs_test_batch_1', $paymentA->gateway_transaction_id);
        $this->assertSame('cs_test_batch_1', $paymentB->gateway_transaction_id);
        $this->assertSame('cs_test_batch_1', data_get($paymentA->gateway_response, 'checkout_session_id'));
        $this->assertSame('cs_test_batch_1', data_get($paymentB->gateway_response, 'checkout_session_id'));

        $this->assertSame('cs_test_batch_1', $orderA->transaction_id);
        $this->assertSame('cs_test_batch_1', $orderB->transaction_id);
    }

    public function test_stripe_success_marks_all_split_payments_paid_in_single_reconciliation(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'checkoutToken' => $checkoutToken,
            'orderA' => $orderA,
            'orderB' => $orderB,
            'paymentA' => $paymentA,
            'paymentB' => $paymentB,
        ] = $this->createSplitStripeCheckout($customer, [120, 80]);

        $stripeMock = Mockery::mock(StripeGatewayService::class);
        $stripeMock->shouldReceive('retrieveCheckoutSession')
            ->once()
            ->with('cs_success_1')
            ->andReturn([
                'id' => 'cs_success_1',
                'payment_status' => 'paid',
                'amount_total' => 20000,
                'payment_intent' => 'pi_batch_paid_1',
                'metadata' => [
                    'payment_id' => (string) $paymentA->id,
                    'payment_ids' => $paymentA->id . ',' . $paymentB->id,
                    'order_number' => $orderA->order_number,
                    'checkout_token' => $checkoutToken,
                ],
            ]);

        $this->app->instance(StripeGatewayService::class, $stripeMock);

        $response = $this
            ->actingAs($customer)
            ->get(route('payment.stripe.success', $orderA->order_number) . '?session_id=cs_success_1');

        $response->assertRedirect(route('checkout.success', $orderA->order_number));
        $response->assertSessionHas('success', 'Stripe payment completed for all split vendor orders.');

        $paymentA->refresh();
        $paymentB->refresh();
        $orderA->refresh();
        $orderB->refresh();

        $this->assertSame('completed', $paymentA->status);
        $this->assertSame('completed', $paymentB->status);
        $this->assertSame('pi_batch_paid_1', $paymentA->gateway_transaction_id);
        $this->assertSame('pi_batch_paid_1', $paymentB->gateway_transaction_id);
        $this->assertNotNull($paymentA->paid_at);
        $this->assertNotNull($paymentB->paid_at);

        $this->assertSame('paid', $orderA->payment_status);
        $this->assertSame('paid', $orderB->payment_status);
        $this->assertSame('pi_batch_paid_1', $orderA->transaction_id);
        $this->assertSame('pi_batch_paid_1', $orderB->transaction_id);
    }

    public function test_stripe_webhook_marks_split_payments_failed_on_amount_mismatch(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'checkoutToken' => $checkoutToken,
            'orderA' => $orderA,
            'orderB' => $orderB,
            'paymentA' => $paymentA,
            'paymentB' => $paymentB,
        ] = $this->createSplitStripeCheckout($customer, [100, 70]);

        $stripeMock = Mockery::mock(StripeGatewayService::class);
        $stripeMock->shouldReceive('verifyWebhookSignature')->once()->andReturn(true);

        $this->app->instance(StripeGatewayService::class, $stripeMock);

        $payload = [
            'id' => 'evt_amount_mismatch_1',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_evt_1',
                    'payment_status' => 'paid',
                    'amount_total' => 1,
                    'metadata' => [
                        'payment_id' => (string) $paymentA->id,
                        'payment_ids' => $paymentA->id . ',' . $paymentB->id,
                        'order_number' => $orderA->order_number,
                        'checkout_token' => $checkoutToken,
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('payment.stripe.webhook'), $payload, [
            'Stripe-Signature' => 'test-signature',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Webhook processed']);

        $paymentA->refresh();
        $paymentB->refresh();
        $orderA->refresh();
        $orderB->refresh();

        $this->assertSame('failed', $paymentA->status);
        $this->assertSame('failed', $paymentB->status);
        $this->assertSame('failed', $orderA->payment_status);
        $this->assertSame('failed', $orderB->payment_status);
    }

    public function test_checkout_success_page_shows_pending_until_all_split_payments_complete(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'orderA' => $orderA,
            'orderB' => $orderB,
        ] = $this->createSplitStripeCheckout($customer, [150, 90]);

        $response = $this
            ->actingAs($customer)
            ->get(route('checkout.success', $orderA->order_number));

        $response->assertOk();
        $response->assertSee('Order Placed, Payment Pending');
        $response->assertSee('Split Checkout Summary');
        $response->assertSee($orderA->order_number);
        $response->assertSee($orderB->order_number);
        $response->assertSee('Complete Payment');
    }

    public function test_checkout_success_page_shows_completed_after_all_split_payments_are_paid(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'orderA' => $orderA,
            'paymentA' => $paymentA,
            'paymentB' => $paymentB,
        ] = $this->createSplitStripeCheckout($customer, [180, 120]);

        $paymentA->markAsPaid('pi_completed_1');
        $paymentB->markAsPaid('pi_completed_1');

        $response = $this
            ->actingAs($customer)
            ->get(route('checkout.success', $orderA->order_number));

        $response->assertOk();
        $response->assertSee('Order Placed Successfully!');
        $response->assertDontSee('Order Placed, Payment Pending');
        $response->assertDontSee('Complete Payment');
    }

    /**
     * @param  array{0:int|float,1:int|float}  $amounts
     * @return array{checkoutToken:string,orderA:Order,orderB:Order,paymentA:\App\Models\Payment,paymentB:\App\Models\Payment}
     */
    private function createSplitStripeCheckout($customer, array $amounts = [110, 220]): array
    {
        $vendorA = $this->createApprovedVendor();
        $vendorB = $this->createApprovedVendor();
        $checkoutToken = (string) Str::uuid();

        $orderA = $this->createOrderForUser($customer, $vendorA, [
            'checkout_token' => $checkoutToken,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => 'stripe',
            'subtotal' => $amounts[0],
            'total' => $amounts[0],
            'commission_amount' => 0,
            'vendor_earning' => $amounts[0],
        ]);

        $orderB = $this->createOrderForUser($customer, $vendorB, [
            'checkout_token' => $checkoutToken,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => 'stripe',
            'subtotal' => $amounts[1],
            'total' => $amounts[1],
            'commission_amount' => 0,
            'vendor_earning' => $amounts[1],
        ]);

        $paymentA = $this->createPaymentForOrder($orderA, [
            'payment_method' => 'stripe',
            'amount' => $amounts[0],
            'status' => 'pending',
        ]);

        $paymentB = $this->createPaymentForOrder($orderB, [
            'payment_method' => 'stripe',
            'amount' => $amounts[1],
            'status' => 'pending',
        ]);

        return compact('checkoutToken', 'orderA', 'orderB', 'paymentA', 'paymentB');
    }
}
