<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\StripeGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_stripe_webhook_rejects_invalid_signature(): void
    {
        $stripeMock = Mockery::mock(StripeGatewayService::class);
        $stripeMock->shouldReceive('verifyWebhookSignature')->once()->andReturn(false);
        $this->app->instance(StripeGatewayService::class, $stripeMock);

        $response = $this->postJson(route('payment.stripe.webhook'), [
            'id' => 'evt_invalid_signature',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_invalid',
                ],
            ],
        ], [
            'Stripe-Signature' => 'invalid-signature',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid signature']);
    }

    public function test_stripe_webhook_marks_split_payments_paid_when_amount_matches(): void
    {
        $customer = $this->createUserWithRole('customer');

        [
            'checkoutToken' => $checkoutToken,
            'orderA' => $orderA,
            'orderB' => $orderB,
            'paymentA' => $paymentA,
            'paymentB' => $paymentB,
        ] = $this->createSplitStripeCheckout($customer, [130, 70]);

        $stripeMock = Mockery::mock(StripeGatewayService::class);
        $stripeMock->shouldReceive('verifyWebhookSignature')->once()->andReturn(true);
        $this->app->instance(StripeGatewayService::class, $stripeMock);

        $payload = [
            'id' => 'evt_split_paid_1',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_paid_split_1',
                    'payment_status' => 'paid',
                    'amount_total' => 20000,
                    'payment_intent' => 'pi_split_paid_1',
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
            'Stripe-Signature' => 'valid-signature',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Webhook processed']);

        $paymentA->refresh();
        $paymentB->refresh();
        $orderA->refresh();
        $orderB->refresh();

        $this->assertSame('completed', $paymentA->status);
        $this->assertSame('completed', $paymentB->status);
        $this->assertSame('pi_split_paid_1', $paymentA->gateway_transaction_id);
        $this->assertSame('pi_split_paid_1', $paymentB->gateway_transaction_id);

        $this->assertSame('paid', $orderA->payment_status);
        $this->assertSame('paid', $orderB->payment_status);
        $this->assertSame('pi_split_paid_1', $orderA->transaction_id);
        $this->assertSame('pi_split_paid_1', $orderB->transaction_id);
    }

    /**
     * @param  array{0:int|float,1:int|float}  $amounts
     * @return array{
     *     checkoutToken:string,
     *     orderA:Order,
     *     orderB:Order,
     *     paymentA:\App\Models\Payment,
     *     paymentB:\App\Models\Payment
     * }
     */
    private function createSplitStripeCheckout($customer, array $amounts): array
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

