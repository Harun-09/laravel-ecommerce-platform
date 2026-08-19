<?php

namespace App\Services;

use App\Domains\ECommerce\Models\MonitoringAlert;
use App\Domains\ECommerce\Models\PaymentEventLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MonitoringAlertService
{
    public function checkPaymentFailureThreshold(?string $provider, ?string $paymentMethod = null): void
    {
        if (!Schema::hasTable('payment_event_logs') || !Schema::hasTable('monitoring_alerts')) {
            return;
        }

        $normalizedProvider = strtolower(trim((string) $provider));
        if ($normalizedProvider === '') {
            $normalizedProvider = 'system';
        }

        $windowMinutes = max(1, (int) config('observability.payment_failure_alert.window_minutes', 15));
        $threshold = max(1, (int) config('observability.payment_failure_alert.threshold', 3));
        $windowStart = now()->subMinutes($windowMinutes);

        $failureCount = PaymentEventLog::query()
            ->where('provider', $normalizedProvider)
            ->where('happened_at', '>=', $windowStart)
            ->where(function ($query): void {
                $query->where('status', 'failed')
                    ->orWhereIn('severity', ['error', 'critical']);
            })
            ->count();

        if ($failureCount < $threshold) {
            return;
        }

        $source = 'payment:' . $normalizedProvider;
        $existingOpenAlert = MonitoringAlert::query()
            ->open()
            ->where('type', 'payment_failures')
            ->where('source', $source)
            ->where('triggered_at', '>=', $windowStart)
            ->exists();

        if ($existingOpenAlert) {
            return;
        }

        $methodInfo = trim((string) $paymentMethod) !== '' ? ' (' . trim((string) $paymentMethod) . ')' : '';
        MonitoringAlert::query()->create([
            'type' => 'payment_failures',
            'severity' => 'critical',
            'source' => $source,
            'title' => 'Payment failure spike detected',
            'description' => sprintf(
                '%d payment failures were observed for %s%s in the last %d minutes.',
                $failureCount,
                strtoupper($normalizedProvider),
                $methodInfo,
                $windowMinutes
            ),
            'context' => [
                'provider' => $normalizedProvider,
                'payment_method' => $paymentMethod,
                'failure_count' => $failureCount,
                'window_minutes' => $windowMinutes,
                'threshold' => $threshold,
            ],
            'status' => MonitoringAlert::STATUS_OPEN,
            'triggered_at' => now(),
        ]);
    }

    public function handleAuditEvent(string $event, ?int $vendorId = null, array $meta = []): void
    {
        if (!Schema::hasTable('monitoring_alerts')) {
            return;
        }

        $criticalEvents = collect(config('observability.audit_critical_events', []))
            ->map(fn($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        if (!in_array($event, $criticalEvents, true)) {
            return;
        }

        $sourceParts = ['audit', $event];
        if ($vendorId && $vendorId > 0) {
            $sourceParts[] = 'vendor:' . $vendorId;
        }
        $source = implode(':', $sourceParts);

        $existing = MonitoringAlert::query()
            ->open()
            ->where('type', 'audit_critical')
            ->where('source', $source)
            ->where('triggered_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($existing) {
            return;
        }

        MonitoringAlert::query()->create([
            'type' => 'audit_critical',
            'severity' => 'warning',
            'source' => $source,
            'title' => 'Critical audit event: ' . $event,
            'description' => 'A configured critical audit event was recorded and requires review.',
            'context' => [
                'event' => $event,
                'vendor_id' => $vendorId,
                'meta' => $meta,
            ],
            'status' => MonitoringAlert::STATUS_OPEN,
            'triggered_at' => now(),
        ]);
    }

    public function safeHandleAuditEvent(string $event, ?int $vendorId = null, array $meta = []): void
    {
        try {
            $this->handleAuditEvent($event, $vendorId, $meta);
        } catch (Throwable $exception) {
            Log::warning('Monitoring alert creation from audit event failed.', [
                'event' => $event,
                'vendor_id' => $vendorId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
