<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use App\Services\PaymentEventLogger;
use App\Services\SslCommerzService;
use App\Services\StripeGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private SslCommerzService $sslCommerz,
        private StripeGatewayService $stripeGateway,
        private PaymentEventLogger $paymentEventLogger
    ) {
    }

    // ===================================================================
    // Payment Process â€” Decides which gateway to use
    // ===================================================================

    public function process(string $orderNumber): RedirectResponse
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->with('payments')
            ->firstOrFail();

        $this->logPaymentEvent(
            'payment.process_requested',
            $order,
            $order->payments->sortByDesc('id')->first(),
            'info',
            null,
            ['order_number' => $orderNumber, 'payment_method' => $order->payment_method],
            'Payment process route requested by customer.',
            $order->payments->isNotEmpty()
        );

        // Route to the correct gateway
        if ($order->payment_method === 'sslcommerz') {
            return $this->processSslcommerz($order);
        }

        if ($order->payment_method === 'stripe') {
            return $this->processStripe($order);
        }

        $this->logPaymentEvent(
            'payment.gateway_unsupported',
            $order,
            $order->payments->sortByDesc('id')->first(),
            'warning',
            null,
            ['payment_method' => $order->payment_method],
            'Selected payment method is not supported.'
        );

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('error', 'Selected payment method is not supported yet.');
    }

    // ===================================================================
    // SSLCOMMERZ Payment Methods
    // ===================================================================

    private function processSslcommerz($order): RedirectResponse
    {
        $payment = $order->payments()->latest()->first();

        if (!$payment) {
            $this->logPaymentEvent(
                'sslcommerz.payment_missing',
                $order,
                null,
                'error',
                'failed',
                ['order_number' => $order->order_number],
                'No payment record found for SSLCOMMERZ order processing.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment record not found for this order.');
        }

        if ($payment->isCompleted() || $order->payment_status === 'paid') {
            $this->logPaymentEvent(
                'sslcommerz.already_paid',
                $order,
                $payment,
                'info',
                'completed',
                ['order_number' => $order->order_number],
                'SSLCOMMERZ process requested for already completed payment.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Payment already completed for this order.');
        }

        if (!$this->sslCommerz->isConfigured()) {
            $this->logPaymentEvent(
                'sslcommerz.gateway_not_configured',
                $order,
                $payment,
                'error',
                'failed',
                ['order_number' => $order->order_number],
                'SSLCOMMERZ gateway is not configured.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'SSLCOMMERZ gateway is not configured. Please contact support.');
        }

        $response = $this->sslCommerz->initiatePayment($order, $payment);

        if (isset($response['error']) || empty($response['GatewayPageURL'])) {
            $payment->update([
                'status' => 'pending',
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'sslcommerz_error' => $response['error'] ?? 'Unknown SSLCOMMERZ error',
                    'sslcommerz_error_at' => now()->toIso8601String(),
                ]),
            ]);

            $this->logPaymentEvent(
                'sslcommerz.checkout_initiation_failed',
                $order,
                $payment,
                'error',
                'failed',
                ['response' => $response],
                'Unable to initiate SSLCOMMERZ checkout.',
                !empty((array) $payment->gateway_response)
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Unable to initiate SSLCOMMERZ checkout right now.');
        }

        $payment->update([
            'status' => 'processing',
            'gateway_response' => array_merge((array) $payment->gateway_response, [
                'sslcommerz_sessionkey' => $response['sessionkey'] ?? null,
                'sslcommerz_redirect_url' => $response['GatewayPageURL'] ?? null,
                'sslcommerz_initiated_at' => now()->toIso8601String(),
            ]),
        ]);

        $this->logPaymentEvent(
            'sslcommerz.checkout_initiated',
            $order,
            $payment,
            'info',
            'processing',
            [
                'gateway_url' => $response['GatewayPageURL'] ?? null,
                'session_key' => $response['sessionkey'] ?? null,
            ],
            'SSLCOMMERZ checkout session created.',
            !empty((array) $payment->gateway_response)
        );

        return redirect()->away($response['GatewayPageURL']);
    }

    /**
     * SSLCOMMERZ success redirect (POST from SSLCOMMERZ).
     */
    public function sslcommerzSuccess(Request $request, string $orderNumber): RedirectResponse
    {
        Log::info('SSLCOMMERZ success redirect received', [
            'order_number' => $orderNumber,
            'data' => $request->all(),
        ]);

        // SSLCOMMERZ POSTs here â€” no auth session, look up order directly
        $order = Order::where('order_number', $orderNumber)
            ->with('payments')
            ->firstOrFail();

        $payment = $order->payments()->latest()->first();

        $this->logPaymentEvent(
            'sslcommerz.success_redirect_received',
            $order,
            $payment,
            'info',
            $payment?->status,
            ['request_data' => $request->all()],
            'SSLCOMMERZ success callback received.'
        );

        if (!$payment) {
            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment record not found.');
        }

        if ($payment->isCompleted() || $order->payment_status === 'paid') {
            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Payment already confirmed.');
        }

        $data = $request->all();
        $tranId = $data['tran_id'] ?? '';
        $valId = $data['val_id'] ?? '';
        $status = strtoupper($data['status'] ?? '');

        if ($valId && $status === 'VALID') {
            $validation = $this->sslCommerz->validateOrder($valId);

            if ($this->sslCommerz->isTransactionValid($validation)) {
                $validatedAmount = (float) ($validation['amount'] ?? 0);
                $expectedAmount = (float) $payment->amount;

                if (abs($validatedAmount - $expectedAmount) < 0.01) {
                    $payment->markAsPaid(
                        $validation['tran_id'] ?? $tranId,
                        $this->buildSslGatewayResponse($payment, 'success_redirect', $data, $validation)
                    );

                    $order->update(['transaction_id' => $validation['tran_id'] ?? $tranId]);

                    $this->logPaymentEvent(
                        'sslcommerz.payment_completed',
                        $order,
                        $payment,
                        'info',
                        'completed',
                        [
                            'transaction_id' => $validation['tran_id'] ?? $tranId,
                            'validated_amount' => $validatedAmount,
                            'expected_amount' => $expectedAmount,
                        ],
                        'SSLCOMMERZ payment marked as paid from success redirect.'
                    );

                    return redirect()->to($this->checkoutSuccessRoute($order))
                        ->with('success', 'SSLCOMMERZ payment completed successfully!');
                } else {
                    Log::warning('SSLCOMMERZ: Amount mismatch on success', [
                        'expected' => $expectedAmount,
                        'validated' => $validatedAmount,
                        'order_number' => $orderNumber,
                    ]);

                    $payment->markAsFailed(
                        $this->buildSslGatewayResponse($payment, 'amount_mismatch', $data, $validation)
                    );

                    $this->logPaymentEvent(
                        'sslcommerz.amount_mismatch',
                        $order,
                        $payment,
                        'error',
                        'failed',
                        [
                            'transaction_id' => $validation['tran_id'] ?? $tranId,
                            'validated_amount' => $validatedAmount,
                            'expected_amount' => $expectedAmount,
                        ],
                        'SSLCOMMERZ amount mismatch detected.'
                    );

                    return redirect()->to($this->checkoutSuccessRoute($order))
                        ->with('error', 'Payment amount mismatch. Please contact support.');
                }
            }
        }

        $payment->update([
            'gateway_response' => $this->buildSslGatewayResponse($payment, 'success_redirect_pending', $data),
        ]);

        $this->logPaymentEvent(
            'sslcommerz.success_redirect_pending',
            $order,
            $payment,
            'warning',
            $payment->status,
            ['request_data' => $data],
            'SSLCOMMERZ success redirect received but payment verification is pending.'
        );

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('success', 'Order placed. Payment confirmation is pending.');
    }

    /**
     * SSLCOMMERZ fail redirect (POST from SSLCOMMERZ).
     */
    public function sslcommerzFail(Request $request, string $orderNumber): RedirectResponse
    {
        Log::info('SSLCOMMERZ fail redirect received', [
            'order_number' => $orderNumber,
            'data' => $request->all(),
        ]);

        // SSLCOMMERZ POSTs here â€” no auth session, look up order directly
        $order = Order::where('order_number', $orderNumber)
            ->with('payments')
            ->firstOrFail();

        $payment = $order->payments()->latest()->first();

        if ($payment && !$payment->isCompleted()) {
            $payment->markAsFailed(
                $this->buildSslGatewayResponse($payment, 'payment_failed', $request->all())
            );
        }

        $this->logPaymentEvent(
            'sslcommerz.payment_failed_redirect',
            $order,
            $payment,
            'error',
            'failed',
            ['request_data' => $request->all()],
            'SSLCOMMERZ fail callback received.'
        );

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('error', 'Payment failed. You can retry payment from your order details.');
    }

    /**
     * SSLCOMMERZ cancel redirect (GET from SSLCOMMERZ).
     */
    public function sslcommerzCancel(Request $request, string $orderNumber): RedirectResponse
    {
        Log::info('SSLCOMMERZ cancel redirect received', [
            'order_number' => $orderNumber,
            'data' => $request->all(),
        ]);

        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->with('payments')
            ->firstOrFail();

        $payment = $order->payments()->latest()->first();

        if ($payment && !$payment->isCompleted()) {
            $payment->update([
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'sslcommerz_cancelled_at' => now()->toIso8601String(),
                    'cancel_data' => $request->all(),
                ]),
            ]);
        }

        $this->logPaymentEvent(
            'sslcommerz.payment_cancelled',
            $order,
            $payment,
            'warning',
            $payment?->status,
            ['request_data' => $request->all()],
            'Customer cancelled SSLCOMMERZ payment flow.'
        );

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('error', 'Payment was cancelled. You can retry payment later.');
    }

    /**
     * SSLCOMMERZ IPN (Instant Payment Notification) webhook.
     */
    public function sslcommerzIPN(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('SSLCOMMERZ IPN received', ['data' => $data]);

        if (!$this->sslCommerz->verifyIpnHash($data)) {
            Log::warning('SSLCOMMERZ IPN: Hash verification failed.');
            $this->logPaymentEvent(
                'sslcommerz.ipn_invalid_hash',
                null,
                null,
                'error',
                'failed',
                ['request_data' => $data],
                'SSLCOMMERZ IPN hash verification failed.'
            );
            return response()->json(['message' => 'Invalid IPN hash'], 400);
        }

        $tranId = $data['tran_id'] ?? '';
        $valId = $data['val_id'] ?? '';
        $status = strtoupper($data['status'] ?? '');

        $payment = Payment::with('order')
            ->where('transaction_id', $tranId)
            ->latest()
            ->first();

        if (!$payment) {
            $paymentId = (int) ($data['value_a'] ?? 0);
            if ($paymentId > 0) {
                $payment = Payment::with('order')->find($paymentId);
            }
        }

        if (!$payment) {
            Log::info('SSLCOMMERZ IPN: Payment not found', ['tran_id' => $tranId]);
            $this->logPaymentEvent(
                'sslcommerz.ipn_payment_not_found',
                null,
                null,
                'warning',
                'failed',
                ['tran_id' => $tranId, 'request_data' => $data],
                'SSLCOMMERZ IPN received but payment record was not resolved.'
            );
            return response()->json(['message' => 'Payment not found'], 200);
        }

        $this->logPaymentEvent(
            'sslcommerz.ipn_received',
            $payment->order,
            $payment,
            'info',
            $payment->status,
            ['request_data' => $data],
            'SSLCOMMERZ IPN payload received.'
        );

        if ($payment->isCompleted()) {
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($status === 'VALID' && $valId) {
            $validation = $this->sslCommerz->validateOrder($valId);

            if ($this->sslCommerz->isTransactionValid($validation)) {
                $validatedAmount = (float) ($validation['amount'] ?? 0);
                $expectedAmount = (float) $payment->amount;

                if (abs($validatedAmount - $expectedAmount) < 0.01) {
                    $payment->markAsPaid(
                        $validation['tran_id'] ?? $tranId,
                        $this->buildSslGatewayResponse($payment, 'ipn_valid', $data, $validation)
                    );
                    $payment->order->update(['transaction_id' => $validation['tran_id'] ?? $tranId]);

                    $this->logPaymentEvent(
                        'sslcommerz.ipn_payment_completed',
                        $payment->order,
                        $payment,
                        'info',
                        'completed',
                        [
                            'transaction_id' => $validation['tran_id'] ?? $tranId,
                            'validated_amount' => $validatedAmount,
                            'expected_amount' => $expectedAmount,
                        ],
                        'SSLCOMMERZ payment marked as paid from IPN.'
                    );
                } else {
                    $payment->markAsFailed(
                        $this->buildSslGatewayResponse($payment, 'ipn_amount_mismatch', $data, $validation)
                    );

                    $this->logPaymentEvent(
                        'sslcommerz.ipn_amount_mismatch',
                        $payment->order,
                        $payment,
                        'error',
                        'failed',
                        [
                            'transaction_id' => $validation['tran_id'] ?? $tranId,
                            'validated_amount' => $validatedAmount,
                            'expected_amount' => $expectedAmount,
                        ],
                        'SSLCOMMERZ IPN amount mismatch detected.'
                    );
                }
            }
        } elseif (in_array($status, ['FAILED', 'CANCELLED', 'EXPIRED', 'UNATTEMPTED'], true)) {
            $payment->markAsFailed(
                $this->buildSslGatewayResponse($payment, 'ipn_' . strtolower($status), $data)
            );

            $this->logPaymentEvent(
                'sslcommerz.ipn_payment_failed',
                $payment->order,
                $payment,
                'error',
                'failed',
                ['ipn_status' => $status],
                'SSLCOMMERZ IPN reported a failed/cancelled payment.'
            );
        }

        return response()->json(['message' => 'IPN processed'], 200);
    }

    // ===================================================================
    // Stripe Payment Methods
    // ===================================================================

    private function processStripe(Order $order): RedirectResponse
    {
        if (!$this->stripeGateway->isConfigured()) {
            $this->logPaymentEvent(
                'stripe.gateway_not_configured',
                $order,
                $order->payments()->latest()->first(),
                'error',
                'failed',
                ['order_number' => $order->order_number],
                'Stripe gateway is not configured.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Stripe gateway is not configured. Please contact support.');
        }

        $orders = $this->resolveStripeCheckoutOrders($order);
        $payments = $this->resolveLatestPaymentsForOrders($orders);

        if ($payments->isEmpty()) {
            $this->logPaymentEvent(
                'stripe.payments_not_found',
                $order,
                null,
                'error',
                'failed',
                ['checkout_token' => $order->checkout_token],
                'No payment records found for Stripe checkout batch.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment records not found for this checkout.');
        }

        $chargeablePayments = $payments
            ->filter(fn(Payment $payment) => !$payment->isCompleted() && (string) optional($payment->order)->payment_status !== 'paid')
            ->values();

        if ($chargeablePayments->isEmpty()) {
            $this->logPaymentEvent(
                'stripe.already_paid',
                $order,
                $payments->first(),
                'info',
                'completed',
                ['checkout_token' => $order->checkout_token],
                'Stripe checkout requested but all payments were already completed.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Payment already completed for this checkout.');
        }

        $session = $this->stripeGateway->createCheckoutSessionForOrders($order, $orders, $chargeablePayments);

        if (isset($session['error']) || empty($session['url'])) {
            foreach ($chargeablePayments as $payment) {
                $payment->update([
                    'status' => 'pending',
                    'gateway_response' => array_merge((array) $payment->gateway_response, [
                        'checkout_error' => $session['error'] ?? 'Unknown Stripe error',
                        'checkout_error_at' => now()->toIso8601String(),
                    ]),
                ]);

                $this->logPaymentEvent(
                    'stripe.checkout_initiation_failed',
                    $payment->order,
                    $payment,
                    'error',
                    'failed',
                    ['stripe_response' => $session],
                    'Stripe checkout session creation failed.',
                    true
                );
            }

            $errorMessage = $session['error'] ?? 'Unable to initiate Stripe checkout right now.';
            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', $errorMessage);
        }

        foreach ($chargeablePayments as $payment) {
            $payment->update([
                'status' => 'processing',
                'gateway_transaction_id' => $session['id'] ?? $payment->gateway_transaction_id,
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'checkout_session_id' => $session['id'] ?? null,
                    'checkout_url' => $session['url'] ?? null,
                'checkout_created_at' => now()->toIso8601String(),
            ]),
        ]);

            $this->logPaymentEvent(
                'stripe.checkout_initiated',
                $payment->order,
                $payment,
                'info',
                'processing',
                [
                    'session_id' => $session['id'] ?? null,
                    'checkout_url' => $session['url'] ?? null,
                ],
                'Stripe checkout session created.',
                true
            );
        }

        $chargeableOrderIds = $chargeablePayments
            ->map(fn(Payment $payment) => (int) $payment->order_id)
            ->filter(fn(int $orderId) => $orderId > 0)
            ->unique()
            ->all();

        foreach ($orders->whereIn('id', $chargeableOrderIds) as $checkoutOrder) {
            $checkoutOrder->update([
                'transaction_id' => $session['id'] ?? $checkoutOrder->transaction_id,
            ]);
        }

        return redirect()->away($session['url']);
    }

    public function stripeSuccess(Request $request, string $orderNumber): RedirectResponse
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $sessionId = (string) $request->query('session_id', '');
        $latestPayment = $order->payments()->latest()->first();

        if ($sessionId === '') {
            $this->logPaymentEvent(
                'stripe.success_redirect_without_session',
                $order,
                $latestPayment,
                'warning',
                $latestPayment?->status,
                ['order_number' => $orderNumber],
                'Stripe success redirect received without session_id.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Order placed. Payment confirmation is pending.');
        }

        $session = $this->stripeGateway->retrieveCheckoutSession($sessionId);
        if (isset($session['error'])) {
            $this->logPaymentEvent(
                'stripe.session_retrieve_failed',
                $order,
                $latestPayment,
                'error',
                $latestPayment?->status,
                ['session_id' => $sessionId, 'stripe_error' => $session['error']],
                'Stripe session retrieval failed on success redirect.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Order placed. Payment confirmation is pending webhook.');
        }

        $payment = $this->resolvePaymentFromStripeObject($session);
        if (!$payment) {
            $payment = $order->payments()->latest()->first()?->loadMissing('order');
        }

        if (!$payment) {
            $this->logPaymentEvent(
                'stripe.payment_not_found',
                $order,
                null,
                'error',
                'failed',
                ['session_id' => $sessionId],
                'Stripe success redirect could not resolve payment record.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment record not found.');
        }

        if (!$this->isStripeSessionForOrder($order, $session)) {
            Log::warning('Stripe success session/order mismatch', [
                'route_order_number' => $order->order_number,
                'session_id' => $sessionId,
                'session_metadata' => data_get($session, 'metadata', []),
            ]);

            $this->logPaymentEvent(
                'stripe.session_order_mismatch',
                $order,
                $payment,
                'error',
                'failed',
                [
                    'session_id' => $sessionId,
                    'session_metadata' => data_get($session, 'metadata', []),
                ],
                'Stripe success session does not match route order.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment session does not match this order.');
        }

        if (!$this->isStripeMetadataValid($payment, $order->order_number, $session)) {
            Log::warning('Stripe success metadata mismatch', [
                'order_number' => $order->order_number,
                'payment_id' => $payment->id,
                'session_id' => $sessionId,
                'metadata' => data_get($session, 'metadata', []),
            ]);

            $payment->markAsFailed($this->buildStripeGatewayResponse(
                $payment,
                '',
                'success_redirect_metadata_mismatch',
                $session,
                ['reason' => 'stripe_metadata_mismatch']
            ));

            $this->logPaymentEvent(
                'stripe.metadata_mismatch',
                $order,
                $payment,
                'error',
                'failed',
                [
                    'session_id' => $sessionId,
                    'metadata' => data_get($session, 'metadata', []),
                ],
                'Stripe metadata validation failed on success redirect.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        $batchPayments = $this->resolveBatchPayments($payment, $session);
        if ($batchPayments->isEmpty()) {
            $batchPayments = collect([$payment]);
        }

        if ($batchPayments->every(fn(Payment $item) => $item->isCompleted())) {
            $this->logPaymentEvent(
                'stripe.success_already_processed',
                $order,
                $payment,
                'info',
                'completed',
                ['session_id' => $sessionId],
                'Stripe success redirect received for already completed batch.'
            );

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', 'Payment confirmed successfully.');
        }

        if ($this->isStripeObjectPaid('checkout.session.completed', $session) && $this->isPaymentAmountValidForPayments($batchPayments, $session)) {
            $paymentIntent = data_get($session, 'payment_intent');
            $transactionId = is_array($paymentIntent) ? data_get($paymentIntent, 'id') : (string) ($paymentIntent ?: $sessionId);

            $this->markStripePaymentsAsPaid(
                $batchPayments,
                $transactionId,
                '',
                'success_redirect',
                $session
            );

            foreach ($batchPayments as $batchPayment) {
                if (!$batchPayment instanceof Payment) {
                    continue;
                }

                $this->logPaymentEvent(
                    'stripe.payment_completed',
                    $batchPayment->order,
                    $batchPayment,
                    'info',
                    'completed',
                    ['session_id' => $sessionId, 'transaction_id' => $transactionId],
                    'Stripe payment marked as completed from success redirect.'
                );
            }

            $message = $batchPayments->count() > 1
                ? 'Stripe payment completed for all split vendor orders.'
                : 'Stripe payment completed successfully.';
            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('success', $message);
        }

        if ($this->isStripeObjectPaid('checkout.session.completed', $session) && !$this->isPaymentAmountValidForPayments($batchPayments, $session)) {
            $this->markStripePaymentsAsFailed(
                $batchPayments,
                '',
                'success_redirect_amount_mismatch',
                $session,
                ['reason' => 'stripe_amount_mismatch']
            );

            foreach ($batchPayments as $batchPayment) {
                if (!$batchPayment instanceof Payment) {
                    continue;
                }

                $this->logPaymentEvent(
                    'stripe.amount_mismatch',
                    $batchPayment->order,
                    $batchPayment,
                    'error',
                    'failed',
                    ['session_id' => $sessionId],
                    'Stripe amount mismatch detected on success redirect.'
                );
            }

            return redirect()->to($this->checkoutSuccessRoute($order))
                ->with('error', 'Payment amount mismatch. Please contact support.');
        }

        $this->logPaymentEvent(
            'stripe.success_pending_verification',
            $order,
            $payment,
            'warning',
            $payment->status,
            ['session_id' => $sessionId],
            'Stripe success redirect received but payment remains pending verification.'
        );

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('success', 'Order placed. Payment verification is still pending.');
    }

    public function stripeCancel(string $orderNumber): RedirectResponse
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $payments = $this->resolveLatestPaymentsForOrders($this->resolveStripeCheckoutOrders($order));

        foreach ($payments as $payment) {
            if ($payment->isCompleted()) {
                continue;
            }

            $payment->update([
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'checkout_cancelled_at' => now()->toIso8601String(),
                ]),
            ]);

            $this->logPaymentEvent(
                'stripe.checkout_cancelled',
                $payment->order,
                $payment,
                'warning',
                $payment->status,
                ['order_number' => $orderNumber],
                'Stripe checkout was cancelled by customer.',
                true
            );
        }

        $message = $payments->count() > 1
            ? 'Stripe payment was cancelled for this split checkout. You can retry payment later.'
            : 'Stripe payment was cancelled. You can retry payment later.';

        return redirect()->to($this->checkoutSuccessRoute($order))
            ->with('error', $message);
    }

    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$this->stripeGateway->verifyWebhookSignature($signature, $payload)) {
            Log::warning('Stripe webhook signature verification failed.');
            $this->logPaymentEvent(
                'stripe.webhook_invalid_signature',
                null,
                null,
                'error',
                'failed',
                ['stripe_signature' => $signature],
                'Stripe webhook signature verification failed.'
            );
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);
        if (!is_array($event) || empty($event['type']) || !is_array(data_get($event, 'data.object'))) {
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        $eventType = (string) $event['type'];
        $eventId = (string) ($event['id'] ?? '');
        $object = data_get($event, 'data.object', []);

        $payment = $this->resolvePaymentFromStripeObject($object);
        if (!$payment) {
            Log::info('Stripe webhook payment not resolved.', [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'object_id' => data_get($object, 'id'),
            ]);

            $this->logPaymentEvent(
                'stripe.webhook_payment_not_resolved',
                null,
                null,
                'warning',
                'failed',
                [
                    'event_id' => $eventId,
                    'event_type' => $eventType,
                    'object_id' => data_get($object, 'id'),
                ],
                'Stripe webhook received but payment could not be resolved.'
            );

            return response()->json(['message' => 'Webhook received'], 200);
        }

        $this->logPaymentEvent(
            'stripe.webhook_received',
            $payment->order,
            $payment,
            'info',
            $payment->status,
            [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'object_id' => data_get($object, 'id'),
            ],
            'Stripe webhook event received for payment.'
        );

        $payments = $this->resolveBatchPayments($payment, $object);
        if ($payments->isEmpty()) {
            $payments = collect([$payment]);
        }

        if (
            $eventId !== '' &&
            $payments->every(fn(Payment $item) => $this->hasProcessedEvent($item, $eventId))
        ) {
            return response()->json(['message' => 'Already processed'], 200);
        }

        $paymentOrderNumber = (string) optional($payment->order)->order_number;

        if (!$this->isStripeMetadataValid($payment, $paymentOrderNumber, $object)) {
            $this->markStripePaymentsAsFailed(
                $payments,
                $eventId,
                $eventType,
                $object,
                ['reason' => 'stripe_metadata_mismatch']
            );

            foreach ($payments as $item) {
                if (!$item instanceof Payment) {
                    continue;
                }

                $this->logPaymentEvent(
                    'stripe.webhook_metadata_mismatch',
                    $item->order,
                    $item,
                    'error',
                    'failed',
                    ['event_id' => $eventId, 'event_type' => $eventType],
                    'Stripe webhook metadata mismatch detected.'
                );
            }

            return response()->json(['message' => 'Metadata mismatch'], 200);
        }

        if ($this->isSuccessEvent($eventType)) {
            $isPaid = $this->isStripeObjectPaid($eventType, $object);
            $isAmountValid = $this->isPaymentAmountValidForPayments($payments, $object);

            if ($isPaid && $isAmountValid) {
                $paymentIntent = data_get($object, 'payment_intent');
                $transactionId = is_array($paymentIntent)
                    ? (string) data_get($paymentIntent, 'id')
                    : (string) ($paymentIntent ?: data_get($object, 'id') ?: $payment->gateway_transaction_id);

                $this->markStripePaymentsAsPaid(
                    $payments,
                    $transactionId,
                    $eventId,
                    $eventType,
                    $object
                );

                foreach ($payments as $item) {
                    if (!$item instanceof Payment) {
                        continue;
                    }

                    $this->logPaymentEvent(
                        'stripe.webhook_payment_completed',
                        $item->order,
                        $item,
                        'info',
                        'completed',
                        [
                            'event_id' => $eventId,
                            'event_type' => $eventType,
                            'transaction_id' => $transactionId,
                        ],
                        'Stripe webhook marked payment as completed.'
                    );
                }
            } elseif (!$isAmountValid) {
                $this->markStripePaymentsAsFailed(
                    $payments,
                    $eventId,
                    $eventType,
                    $object,
                    ['reason' => 'stripe_amount_mismatch']
                );

                foreach ($payments as $item) {
                    if (!$item instanceof Payment) {
                        continue;
                    }

                    $this->logPaymentEvent(
                        'stripe.webhook_amount_mismatch',
                        $item->order,
                        $item,
                        'error',
                        'failed',
                        ['event_id' => $eventId, 'event_type' => $eventType],
                        'Stripe webhook amount mismatch detected.'
                    );
                }
            } else {
                foreach ($payments as $item) {
                    $item->update([
                        'gateway_response' => $this->buildStripeGatewayResponse($item, $eventId, $eventType, $object, [
                            'note' => 'Received success-type event but payment is not marked paid yet.',
                        ]),
                    ]);

                    $this->logPaymentEvent(
                        'stripe.webhook_success_pending',
                        $item->order,
                        $item,
                        'warning',
                        $item->status,
                        ['event_id' => $eventId, 'event_type' => $eventType],
                        'Stripe success-type webhook received but payment remains pending.'
                    );
                }
            }
        }

        if ($this->isFailureEvent($eventType)) {
            $this->markStripePaymentsAsFailed(
                $payments,
                $eventId,
                $eventType,
                $object
            );

            foreach ($payments as $item) {
                if (!$item instanceof Payment) {
                    continue;
                }

                $this->logPaymentEvent(
                    'stripe.webhook_payment_failed',
                    $item->order,
                    $item,
                    'error',
                    'failed',
                    ['event_id' => $eventId, 'event_type' => $eventType],
                    'Stripe failure webhook marked payment as failed.'
                );
            }
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    private function checkoutSuccessRoute(Order $order): string
    {
        $checkoutToken = trim((string) $order->checkout_token);

        if ($checkoutToken === '') {
            $checkoutToken = (string) Str::uuid();
            $order->forceFill(['checkout_token' => $checkoutToken])->save();
        }

        return route('checkout.success', [
            'orderNumber' => (string) $order->order_number,
            'access_token' => $checkoutToken,
        ]);
    }

    private function logPaymentEvent(
        string $eventType,
        ?Order $order = null,
        ?Payment $payment = null,
        string $severity = 'info',
        ?string $status = null,
        array $context = [],
        ?string $message = null,
        bool $isRetry = false,
        ?string $provider = null
    ): void {
        $this->paymentEventLogger->log($eventType, [
            'order' => $order,
            'payment' => $payment,
            'provider' => $provider ?? ($payment?->payment_method ?: $order?->payment_method),
            'payment_method' => $payment?->payment_method ?: $order?->payment_method,
            'status' => $status ?? $payment?->status ?? $order?->payment_status,
            'severity' => $severity,
            'message' => $message,
            'is_retry' => $isRetry,
            'context' => $context,
            'happened_at' => now(),
        ]);
    }

    // ===================================================================
    // SSLCOMMERZ Helpers
    // ===================================================================

    private function buildSslGatewayResponse(
        Payment $payment,
        string $eventType,
        array $sslData,
        array $validationData = []
    ): array {
        $existing = (array) $payment->gateway_response;

        return array_merge($existing, [
            'last_event' => [
                'type' => $eventType,
                'received_at' => now()->toIso8601String(),
            ],
            'sslcommerz_data' => [
                'tran_id' => $sslData['tran_id'] ?? null,
                'val_id' => $sslData['val_id'] ?? null,
                'status' => $sslData['status'] ?? null,
                'amount' => $sslData['amount'] ?? null,
                'currency' => $sslData['currency'] ?? null,
                'card_type' => $sslData['card_type'] ?? null,
                'card_brand' => $sslData['card_brand'] ?? null,
                'bank_tran_id' => $sslData['bank_tran_id'] ?? null,
                'tran_date' => $sslData['tran_date'] ?? null,
            ],
            'validation_data' => !empty($validationData) ? [
                'status' => $validationData['status'] ?? null,
                'amount' => $validationData['amount'] ?? null,
                'currency_amount' => $validationData['currency_amount'] ?? null,
                'validated_at' => now()->toIso8601String(),
            ] : null,
        ]);
    }

    // ===================================================================
    // Stripe Helpers
    // ===================================================================

    private function isSuccessEvent(string $eventType): bool
    {
        return in_array($eventType, [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
            'payment_intent.succeeded',
        ], true);
    }

    private function isFailureEvent(string $eventType): bool
    {
        return in_array($eventType, [
            'checkout.session.async_payment_failed',
            'payment_intent.payment_failed',
        ], true);
    }

    private function isStripeObjectPaid(string $eventType, array $object): bool
    {
        if (str_starts_with($eventType, 'payment_intent')) {
            return (string) data_get($object, 'status') === 'succeeded';
        }

        return (string) data_get($object, 'payment_status') === 'paid';
    }

    private function resolveStripeCheckoutOrders(Order $order): Collection
    {
        $checkoutToken = trim((string) $order->checkout_token);
        if ($checkoutToken === '') {
            return collect([$order->load('payments')]);
        }

        $orders = Order::query()
            ->where('user_id', $order->user_id)
            ->where('payment_method', 'stripe')
            ->where('checkout_token', $checkoutToken)
            ->with('payments')
            ->orderBy('id')
            ->get();

        return $orders->isNotEmpty()
            ? $orders
            : collect([$order->load('payments')]);
    }

    private function resolveLatestPaymentsForOrders(Collection $orders): Collection
    {
        return $orders
            ->map(function (Order $order) {
                if (!$order->relationLoaded('payments')) {
                    $order->load('payments');
                }

                return $order->payments->sortByDesc('id')->first();
            })
            ->filter(fn($payment) => $payment instanceof Payment)
            ->values()
            ->map(fn(Payment $payment) => $payment->loadMissing('order'));
    }

    private function resolveBatchPayments(Payment $seedPayment, array $stripeObject): Collection
    {
        $paymentIds = $this->extractMetadataPaymentIds($stripeObject);
        if (!empty($paymentIds)) {
            $payments = Payment::query()
                ->with('order')
                ->whereIn('id', $paymentIds)
                ->orderBy('id')
                ->get();

            if ($payments->isNotEmpty()) {
                return $payments->values();
            }
        }

        $checkoutToken = trim((string) data_get($stripeObject, 'metadata.checkout_token', ''));
        if ($checkoutToken !== '') {
            $payments = $this->resolvePaymentsByCheckoutToken($checkoutToken);
            if ($payments->isNotEmpty()) {
                return $payments;
            }
        }

        $seedToken = trim((string) optional($seedPayment->order)->checkout_token);
        if ($seedToken !== '') {
            $payments = $this->resolvePaymentsByCheckoutToken($seedToken);
            if ($payments->isNotEmpty()) {
                return $payments;
            }
        }

        return collect([$seedPayment->loadMissing('order')]);
    }

    private function resolvePaymentsByCheckoutToken(string $checkoutToken): Collection
    {
        return Payment::query()
            ->with('order')
            ->whereHas('order', fn($query) => $query->where('checkout_token', $checkoutToken))
            ->orderByDesc('id')
            ->get()
            ->unique('order_id')
            ->values();
    }

    private function isPaymentAmountValidForPayments(Collection $payments, array $stripeObject): bool
    {
        $expected = (int) round(
            $payments->sum(fn(Payment $payment) => (float) $payment->amount) * 100
        );

        $actual = $this->extractStripeAmountMinor($stripeObject);

        if ($actual === null) {
            return false;
        }

        return $actual === $expected;
    }

    private function extractStripeAmountMinor(array $stripeObject): ?int
    {
        $actual = data_get($stripeObject, 'amount_total');
        if ($actual === null) {
            $actual = data_get($stripeObject, 'amount_received');
        }
        if ($actual === null) {
            $actual = data_get($stripeObject, 'amount');
        }

        if (!is_numeric($actual)) {
            return null;
        }

        return (int) $actual;
    }

    private function isStripeSessionForOrder(Order $order, array $stripeObject): bool
    {
        $metadataCheckoutToken = trim((string) data_get($stripeObject, 'metadata.checkout_token', ''));
        if ($metadataCheckoutToken !== '') {
            return $metadataCheckoutToken === trim((string) $order->checkout_token);
        }

        $metadataOrderNumber = trim((string) data_get($stripeObject, 'metadata.order_number', ''));
        return $metadataOrderNumber !== '' && $metadataOrderNumber === (string) $order->order_number;
    }

    private function isStripeMetadataValid(Payment $payment, string $orderNumber, array $stripeObject): bool
    {
        $metadataPaymentId = data_get($stripeObject, 'metadata.payment_id');
        $metadataOrderNumber = data_get($stripeObject, 'metadata.order_number');
        $metadataCheckoutToken = trim((string) data_get($stripeObject, 'metadata.checkout_token', ''));
        $metadataPaymentIds = $this->extractMetadataPaymentIds($stripeObject);

        if ($metadataPaymentId !== null && (int) $metadataPaymentId !== (int) $payment->id) {
            return false;
        }

        if (!empty($metadataPaymentIds) && !in_array((int) $payment->id, $metadataPaymentIds, true)) {
            return false;
        }

        if ($metadataCheckoutToken !== '') {
            $paymentCheckoutToken = trim((string) optional($payment->order)->checkout_token);
            if ($paymentCheckoutToken !== $metadataCheckoutToken) {
                return false;
            }
        }

        if (
            $metadataOrderNumber !== null &&
            $metadataCheckoutToken === '' &&
            (string) $metadataOrderNumber !== $orderNumber
        ) {
            return false;
        }

        return true;
    }

    private function resolvePaymentFromStripeObject(array $object): ?Payment
    {
        $paymentId = (int) data_get($object, 'metadata.payment_id', 0);
        if ($paymentId > 0) {
            return Payment::with('order')->find($paymentId);
        }

        $paymentIds = $this->extractMetadataPaymentIds($object);
        if (!empty($paymentIds)) {
            $payment = Payment::with('order')
                ->whereIn('id', $paymentIds)
                ->orderBy('id')
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $checkoutToken = trim((string) data_get($object, 'metadata.checkout_token', ''));
        if ($checkoutToken !== '') {
            $payment = $this->resolvePaymentsByCheckoutToken($checkoutToken)->first();
            if ($payment instanceof Payment) {
                return $payment;
            }
        }

        $orderNumber = (string) data_get($object, 'metadata.order_number', '');
        if ($orderNumber !== '') {
            $payment = Payment::with('order')
                ->whereHas('order', fn($query) => $query->where('order_number', $orderNumber))
                ->latest()
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $objectId = (string) data_get($object, 'id', '');
        if ($objectId !== '') {
            $payment = Payment::with('order')
                ->where('gateway_transaction_id', $objectId)
                ->latest()
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $paymentIntentId = (string) data_get($object, 'payment_intent', '');
        if ($paymentIntentId !== '') {
            return Payment::with('order')
                ->where('gateway_transaction_id', $paymentIntentId)
                ->latest()
                ->first();
        }

        return null;
    }

    private function extractMetadataPaymentIds(array $stripeObject): array
    {
        $raw = trim((string) data_get($stripeObject, 'metadata.payment_ids', ''));
        if ($raw === '') {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn(string $id) => (int) trim($id))
            ->filter(fn(int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function markStripePaymentsAsPaid(
        Collection $payments,
        string $transactionId,
        string $eventId,
        string $eventType,
        array $stripeObject
    ): void {
        foreach ($payments as $payment) {
            if (!$payment instanceof Payment) {
                continue;
            }

            if (!$payment->isCompleted()) {
                $payment->markAsPaid(
                    $transactionId,
                    $this->buildStripeGatewayResponse($payment, $eventId, $eventType, $stripeObject)
                );
            } else {
                $payment->update([
                    'gateway_response' => $this->buildStripeGatewayResponse($payment, $eventId, $eventType, $stripeObject),
                ]);
            }

            if ($payment->order) {
                $payment->order->update(['transaction_id' => $transactionId]);
            }
        }
    }

    private function markStripePaymentsAsFailed(
        Collection $payments,
        string $eventId,
        string $eventType,
        array $stripeObject,
        array $extra = []
    ): void {
        foreach ($payments as $payment) {
            if (!$payment instanceof Payment || $payment->isCompleted()) {
                continue;
            }

            $payment->markAsFailed(
                $this->buildStripeGatewayResponse($payment, $eventId, $eventType, $stripeObject, $extra)
            );
        }
    }

    private function hasProcessedEvent(Payment $payment, string $eventId): bool
    {
        if ($eventId === '') {
            return false;
        }

        $processed = data_get($payment->gateway_response, 'processed_event_ids', []);
        return in_array($eventId, is_array($processed) ? $processed : [], true);
    }

    private function buildStripeGatewayResponse(
        Payment $payment,
        string $eventId,
        string $eventType,
        array $stripeObject,
        array $extra = []
    ): array {
        $gatewayResponse = (array) $payment->gateway_response;
        $processedEvents = data_get($gatewayResponse, 'processed_event_ids', []);
        if (!is_array($processedEvents)) {
            $processedEvents = [];
        }

        if ($eventId !== '' && !in_array($eventId, $processedEvents, true)) {
            $processedEvents[] = $eventId;
        }

        return array_merge($gatewayResponse, [
            'processed_event_ids' => $processedEvents,
            'last_webhook_event' => [
                'id' => $eventId ?: null,
                'type' => $eventType,
                'received_at' => now()->toIso8601String(),
            ],
            'webhook_snapshot' => [
                'object_id' => data_get($stripeObject, 'id'),
                'payment_status' => data_get($stripeObject, 'payment_status'),
                'status' => data_get($stripeObject, 'status'),
                'amount_total' => data_get($stripeObject, 'amount_total'),
                'amount_received' => data_get($stripeObject, 'amount_received'),
                'amount' => data_get($stripeObject, 'amount'),
            ],
        ], $extra);
    }
}



