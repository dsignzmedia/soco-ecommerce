<?php

namespace App\Services;

use App\Models\Admin\Master\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    private $keyId;
    private $keySecret;
    private $baseUrl = 'https://api.razorpay.com/v1';

    public function __construct()
    {
        $this->loadCredentials();
    }

    private function loadCredentials()
    {
        $gateway = PaymentGateway::where('provider', 'razorpay')
            ->where('is_active', true)
            ->first();

        if ($gateway && !empty($gateway->credentials)) {
            $this->keyId = $gateway->credentials['key_id'] ?? ($gateway->credentials['key'] ?? null);
            $this->keySecret = $gateway->credentials['key_secret'] ?? ($gateway->credentials['secret'] ?? null);
        }

        if (empty($this->keyId) || empty($this->keySecret)) {
            $this->keyId = env('RAZORPAY_KEY');
            $this->keySecret = env('RAZORPAY_SECRET');
        }
    }

    /**
     * Initiate a refund for a payment
     *
     * @param string $paymentId
     * @param float $amount Amount in INR (will be converted to paise)
     * @param array $notes Optional metadata
     * @return array|null Response data or null on failure
     * @throws \Exception
     */
    public function refund(string $paymentId, float $amount, array $notes = []): ?array
    {
        if (empty($this->keyId) || empty($this->keySecret)) {
            throw new \Exception("Razorpay credentials not configured.");
        }

        $amountInPaise = (int)($amount * 100);

        try {
            $response = Http::withOptions(['verify' => !app()->isLocal()])
                ->withBasicAuth($this->keyId, $this->keySecret)
                ->post("{$this->baseUrl}/payments/{$paymentId}/refund", [
                    'amount' => $amountInPaise,
                    'notes' => $notes,
                    'speed' => 'normal'
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Razorpay Refund Failed', [
                    'payment_id' => $paymentId,
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                throw new \Exception("Razorpay Refund Error: " . ($response->json()['error']['description'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Razorpay Refund Exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
