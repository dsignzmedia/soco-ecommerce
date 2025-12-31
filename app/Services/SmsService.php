<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    /**
     * Send an SMS to the specified number using Creative Point SMS API.
     *
     * @param string $to The phone number to send to (10 digits).
     * @param string $message The message content.
     * @return bool True if successful, false otherwise.
     */
    public function send(string $to, string $message): bool
    {
        try {
            $apiKey = config('services.sms.api_key', '67a09f00f0643');
            $sender = config('services.sms.sender', 'SOCOUS');
            $route = config('services.sms.route', 'transsms');
            $apiUrl = config('services.sms.api_url', 'http://sms.creativepoint.in/api/push.json');

            // Validate phone number format (should be 10 digits)
            if (!preg_match('/^[0-9]{10}$/', $to)) {
                Log::error("SMS Service - Invalid phone number format: {$to}");
                return false;
            }

            // Validate API URL is set
            if (empty($apiUrl)) {
                Log::error("SMS Service - API URL is not configured");
                return false;
            }

            // Build the API URL with query parameters
            $queryParams = http_build_query([
                'apikey' => $apiKey,
                'route' => $route,
                'sender' => $sender,
                'mobileno' => $to,
                'text' => $message,
            ]);

            $url = $apiUrl . '?' . $queryParams;

            Log::info("SMS Service - Sending to {$to}: {$message}");
            Log::info("SMS Service - API URL: {$url}");
            Log::info("SMS Service - Config values", [
                'api_key' => $apiKey,
                'sender' => $sender,
                'route' => $route,
                'api_url' => $apiUrl,
            ]);

            // Make the API request
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                // Check if the API returned success status
                if (isset($responseData['status']) && $responseData['status'] === 'success') {
                    Log::info("SMS Service - Successfully sent to {$to}", [
                        'batchid' => $responseData['description']['batchid'] ?? null,
                        'msgid' => $responseData['description']['batch_dtl'][0]['msgid'] ?? null,
                    ]);
                    return true;
                } else {
                    Log::error("SMS Service - API returned error for {$to}", [
                        'response' => $responseData,
                    ]);
                    return false;
                }
            } else {
                Log::error("SMS Service - HTTP error for {$to}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS Service - Failed to send to {$to}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            return false;
        }
    }
}
