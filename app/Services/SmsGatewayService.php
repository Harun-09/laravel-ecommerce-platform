<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsGatewayService
{
    public function send(string $to, string $message, array $meta = []): bool
    {
        $to = trim($to);
        $message = trim($message);

        if ($to === '' || $message === '') {
            return false;
        }

        $config = (array) config('services.sms', []);
        $enabled = (bool) ($config['enabled'] ?? false);
        $provider = (string) ($config['provider'] ?? 'log');

        if (!$enabled) {
            Log::info('SMS skipped because SMS is disabled.', [
                'to' => $to,
                'provider' => $provider,
            ]);
            return false;
        }

        if ($provider === 'log') {
            Log::info('SMS notification (log provider).', [
                'to' => $to,
                'message' => $message,
                'meta' => $meta,
            ]);

            return true;
        }

        $apiUrl = (string) ($config['api_url'] ?? '');
        $apiToken = (string) ($config['api_token'] ?? '');
        $from = (string) ($config['from'] ?? 'NovaMart');
        $timeout = max(3, (int) ($config['timeout'] ?? 10));

        if ($apiUrl === '') {
            Log::warning('SMS send failed because SMS_API_URL is missing.', [
                'provider' => $provider,
                'to' => $to,
            ]);
            return false;
        }

        $payload = array_merge([
            'to' => $to,
            'from' => $from,
            'message' => $message,
        ], $meta);

        try {
            $request = Http::timeout($timeout)
                ->acceptJson()
                ->asJson();

            if ($apiToken !== '') {
                $request = $request->withToken($apiToken);
            }

            $response = $request->post($apiUrl, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('SMS send failed: non-success response.', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::error('SMS send failed: exception.', [
                'to' => $to,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }
}
