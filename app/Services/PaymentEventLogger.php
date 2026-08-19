<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use App\Domains\ECommerce\Models\PaymentEventLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PaymentEventLogger
{
    public function __construct(
        private MonitoringAlertService $monitoringAlertService
    ) {
    }

    /**
     * @param  array{
     *     order?:Order|int|null,
     *     payment?:Payment|int|null,
     *     provider?:string|null,
     *     payment_method?:string|null,
     *     status?:string|null,
     *     severity?:string|null,
     *     is_retry?:bool|null,
     *     message?:string|null,
     *     context?:array<string,mixed>|null,
     *     happened_at?:\DateTimeInterface|string|null
     * } $payload
     */
    public function log(string $eventType, array $payload = []): void
    {
        if (!Schema::hasTable('payment_event_logs')) {
            return;
        }

        try {
            $order = $this->resolveOrder($payload['order'] ?? null);
            $payment = $this->resolvePayment($payload['payment'] ?? null);

            $provider = strtolower(trim((string) ($payload['provider'] ?? $payment?->payment_method ?? $order?->payment_method ?? 'system')));
            if ($provider === '') {
                $provider = 'system';
            }

            $paymentMethod = trim((string) ($payload['payment_method'] ?? $payment?->payment_method ?? $order?->payment_method ?? ''));
            $status = trim((string) ($payload['status'] ?? $payment?->status ?? $order?->payment_status ?? ''));
            $severity = $this->normalizeSeverity($payload['severity'] ?? null);
            $isRetry = (bool) ($payload['is_retry'] ?? false);

            $context = $payload['context'] ?? [];
            if (!is_array($context)) {
                $context = [];
            }

            if ($order && !isset($context['order_number'])) {
                $context['order_number'] = (string) $order->order_number;
            }
            if ($payment && !isset($context['transaction_id'])) {
                $context['transaction_id'] = (string) $payment->transaction_id;
            }

            PaymentEventLog::query()->create([
                'order_id' => $order?->id,
                'payment_id' => $payment?->id,
                'provider' => $provider,
                'payment_method' => $paymentMethod !== '' ? $paymentMethod : null,
                'event_type' => trim($eventType),
                'status' => $status !== '' ? $status : null,
                'severity' => $severity,
                'is_retry' => $isRetry,
                'message' => $payload['message'] ?? null,
                'context' => $context !== [] ? $context : null,
                'happened_at' => $payload['happened_at'] ?? now(),
            ]);

            if ($this->shouldTriggerFailureAlert($eventType, $status, $severity)) {
                $this->monitoringAlertService->checkPaymentFailureThreshold(
                    $provider,
                    $paymentMethod !== '' ? $paymentMethod : null
                );
            }
        } catch (Throwable $exception) {
            Log::warning('Payment event log write failed.', [
                'event_type' => $eventType,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function resolveOrder(mixed $order): ?Order
    {
        if ($order instanceof Order) {
            return $order;
        }

        if (is_numeric($order)) {
            return Order::query()->find((int) $order);
        }

        return null;
    }

    private function resolvePayment(mixed $payment): ?Payment
    {
        if ($payment instanceof Payment) {
            return $payment;
        }

        if (is_numeric($payment)) {
            return Payment::query()->find((int) $payment);
        }

        return null;
    }

    private function normalizeSeverity(mixed $severity): string
    {
        $value = strtolower(trim((string) $severity));
        return in_array($value, ['info', 'warning', 'error', 'critical'], true) ? $value : 'info';
    }

    private function shouldTriggerFailureAlert(string $eventType, string $status, string $severity): bool
    {
        if (in_array($severity, ['error', 'critical'], true)) {
            return true;
        }

        if (strtolower($status) === 'failed') {
            return true;
        }

        return str_contains(strtolower($eventType), 'fail');
    }
}
