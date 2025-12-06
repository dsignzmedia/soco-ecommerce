<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to the specified number.
     *
     * @param string $to The phone number to send to.
     * @param string $message The message content.
     * @return bool True if successful, false otherwise.
     */
    public function send(string $to, string $message): bool
    {
        // TODO: Integrate with a real SMS provider (e.g., Twilio, Msg91, etc.)
        // For now, we will log the SMS to the application logs for development/testing.

        try {
            Log::info("SMS Service - Sending to {$to}: {$message}");
            
            // Simulation of API call latency
            // usleep(500000); // 0.5 seconds

            return true;
        } catch (\Exception $e) {
            Log::error("SMS Service - Failed to send to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
