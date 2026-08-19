<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorTrackingService
{
    public function report(Throwable $exception, array $context = []): void
    {
        if (!config('observability.error_tracking.enabled', false)) {
            return;
        }

        $provider = strtolower(trim((string) config('observability.error_tracking.provider', 'sentry')));

        try {
            if ($provider === 'sentry') {
                $this->reportToSentry($exception, $context);
                return;
            }

            if ($provider === 'bugsnag') {
                $this->reportToBugsnag($exception, $context);
                return;
            }

            Log::warning('Unsupported error tracking provider configured.', [
                'provider' => $provider,
            ]);
        } catch (Throwable $reportingError) {
            Log::warning('Error tracking provider call failed.', [
                'provider' => $provider,
                'error' => $reportingError->getMessage(),
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function reportToSentry(Throwable $exception, array $context): void
    {
        if (!function_exists('\Sentry\captureException')) {
            Log::warning('Sentry SDK is not installed. Exception logged locally only.', [
                'error' => $exception->getMessage(),
                'context' => $context,
            ]);
            return;
        }

        if (!empty($context) && function_exists('\Sentry\configureScope')) {
            \Sentry\configureScope(function ($scope) use ($context): void {
                foreach ($context as $key => $value) {
                    $scope->setExtra((string) $key, $value);
                }
            });
        }

        \Sentry\captureException($exception);
    }

    private function reportToBugsnag(Throwable $exception, array $context): void
    {
        if (!app()->bound('bugsnag')) {
            Log::warning('Bugsnag client is not available. Exception logged locally only.', [
                'error' => $exception->getMessage(),
                'context' => $context,
            ]);
            return;
        }

        $bugsnag = app('bugsnag');
        if (method_exists($bugsnag, 'notifyException')) {
            $bugsnag->notifyException($exception, function ($report) use ($context): void {
                if (method_exists($report, 'setMetaData') && !empty($context)) {
                    $report->setMetaData(['context' => $context]);
                }
            });
            return;
        }

        Log::warning('Bugsnag client does not support notifyException.', [
            'error' => $exception->getMessage(),
            'context' => $context,
        ]);
    }
}
