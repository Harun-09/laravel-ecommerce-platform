<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    private string $storeId;
    private string $storePassword;
    private bool $sandbox;
    private string $apiUrl;
    private string $validationUrl;
    private string $multiCardName;

    public function __construct()
    {
        $config = config('services.sslcommerz', []);
        $this->sandbox = (bool) ($config['sandbox'] ?? true);
        $this->storeId = (string) ($config['store_id'] ?? '');
        $this->storePassword = (string) ($config['store_password'] ?? '');
        $this->multiCardName = trim((string) ($config['multi_card_name'] ?? ''));

        if ($this->sandbox) {
            $this->apiUrl = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
            $this->validationUrl = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php';
        } else {
            $this->apiUrl = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
            $this->validationUrl = 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->storeId)
            && !empty($this->storePassword)
            && $this->storeId !== 'YOUR_STORE_ID'
            && $this->storePassword !== 'YOUR_STORE_PASSWORD';
    }

    /**
     * Initiate a payment session with SSLCOMMERZ.
     * Returns the gateway page URL or an error array.
     */
    public function initiatePayment(Order $order, Payment $payment): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'SSLCOMMERZ is not configured.'];
        }

        $successUrl = route('payment.sslcommerz.success', $order->order_number, true);
        $failUrl = route('payment.sslcommerz.fail', $order->order_number, true);
        $cancelUrl = route('payment.sslcommerz.cancel', $order->order_number, true);
        $ipnUrl = route('payment.sslcommerz.ipn', [], true);

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => number_format((float) $payment->amount, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $payment->transaction_id,
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,
            'ipn_url' => $ipnUrl,

            // Customer info
            'cus_name' => $order->shipping_name ?? 'Customer',
            'cus_email' => $order->shipping_email ?: ($order->user?->email ?: 'customer@example.com'),
            'cus_phone' => $order->shipping_phone ?? '01700000000',
            'cus_add1' => $order->shipping_address ?? 'Dhaka',
            'cus_city' => $order->shipping_city ?? 'Dhaka',
            'cus_state' => $order->shipping_state ?? 'Dhaka',
            'cus_postcode' => $order->shipping_postal_code ?? '1000',
            'cus_country' => $order->shipping_country ?? 'Bangladesh',

            // Shipping info
            'shipping_method' => 'NO',
            'num_of_item' => $order->items()->count() ?: 1,

            // Product info
            'product_name' => 'Order ' . $order->order_number,
            'product_category' => 'E-Commerce',
            'product_profile' => 'general',

            // Extra value fields to pass metadata
            'value_a' => (string) $payment->id,
            'value_b' => (string) $order->id,
            'value_c' => (string) $order->order_number,
            'value_d' => (string) ($order->user_id ?? ''),
        ];

        if ($this->multiCardName !== '') {
            $postData['multi_card_name'] = $this->multiCardName;
        }

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, $postData);

            $result = $response->json();

            if (!is_array($result)) {
                Log::error('SSLCOMMERZ: Invalid response format', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['error' => 'Invalid response from SSLCOMMERZ.'];
            }

            if (($result['status'] ?? '') !== 'SUCCESS') {
                Log::error('SSLCOMMERZ: Session creation failed', [
                    'response' => $result,
                ]);
                return [
                    'error' => $result['failedreason'] ?? 'Failed to create SSLCOMMERZ session.',
                ];
            }

            return [
                'status' => 'SUCCESS',
                'sessionkey' => $result['sessionkey'] ?? null,
                'GatewayPageURL' => $result['GatewayPageURL'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('SSLCOMMERZ: Connection error', [
                'message' => $e->getMessage(),
            ]);
            return [
                'error' => 'Failed to connect to SSLCOMMERZ.',
                'code' => 'sslcommerz_connection_error',
            ];
        }
    }

    /**
     * Validate a transaction with the SSLCOMMERZ Order Validation API.
     */
    public function validateOrder(string $valId): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'SSLCOMMERZ is not configured.'];
        }

        try {
            $response = Http::timeout(30)->get($this->validationUrl, [
                'val_id' => $valId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json',
            ]);

            $result = $response->json();

            if (!is_array($result)) {
                return ['error' => 'Invalid validation response.'];
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('SSLCOMMERZ: Validation error', [
                'val_id' => $valId,
                'message' => $e->getMessage(),
            ]);
            return ['error' => 'Failed to validate with SSLCOMMERZ.'];
        }
    }

    /**
     * Check if the IPN hash is valid.
     * Verifies the response data integrity using SSLCOMMERZ's hash mechanism.
     */
    public function verifyIpnHash(array $data): bool
    {
        if (empty($data['verify_sign']) || empty($data['verify_key'])) {
            return false;
        }

        $verifySign = $data['verify_sign'];
        $verifyKey = $data['verify_key'];

        // Build the hash string from the verify_key order
        $keys = explode(',', $verifyKey);
        $hashString = '';

        foreach ($keys as $key) {
            $key = trim($key);
            if (isset($data[$key])) {
                $hashString .= $key . '=' . $data[$key] . '&';
            }
        }

        $hashString .= 'store_passwd=' . md5($this->storePassword);
        $generatedHash = md5($hashString);

        return $generatedHash === $verifySign;
    }

    /**
     * Determine if the transaction is in a valid paid state.
     */
    public function isTransactionValid(array $data): bool
    {
        $status = strtoupper($data['status'] ?? '');
        return in_array($status, ['VALID', 'VALIDATED'], true);
    }

    /**
     * Determine if the transaction failed.
     */
    public function isTransactionFailed(array $data): bool
    {
        $status = strtoupper($data['status'] ?? '');
        return in_array($status, ['FAILED', 'EXPIRED', 'UNATTEMPTED'], true);
    }

    /**
     * Determine if the transaction was cancelled.
     */
    public function isTransactionCancelled(array $data): bool
    {
        $status = strtoupper($data['status'] ?? '');
        return $status === 'CANCELLED';
    }
}
