<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Razorpay Config Debug ---\n";

// 1. Check DB
$gateway = \App\Models\Admin\Master\PaymentGateway::where('provider', 'razorpay')
    ->where('is_active', true)
    ->first();

$dbKey = null;
$dbSecret = null;

if ($gateway) {
    echo "Gateway found in DB (ID: {$gateway->id})\n";
    $creds = $gateway->credentials;
    // print_r($creds); 
    
    $dbKey = $creds['key_id'] ?? ($creds['key'] ?? null);
    $dbSecret = $creds['key_secret'] ?? ($creds['secret'] ?? null);
    
    if ($dbKey) echo "DB Key: " . substr($dbKey, 0, 8) . "... (Len: " . strlen($dbKey) . ")\n";
    else echo "DB Key: NULL\n";
    
    if ($dbSecret) echo "DB Secret: " . substr($dbSecret, 0, 8) . "... (Len: " . strlen($dbSecret) . ")\n";
    else echo "DB Secret: NULL\n";

} else {
    echo "No active Razorpay gateway in DB.\n";
}

// 2. Check Env
$envKey = env('RAZORPAY_KEY');
$envSecret = env('RAZORPAY_SECRET');

if ($envKey) echo "ENV Key: " . substr($envKey, 0, 8) . "... (Len: " . strlen($envKey) . ")\n";
else echo "ENV Key: NULL\n";

if ($envSecret) echo "ENV Secret: " . substr($envSecret, 0, 8) . "... (Len: " . strlen($envSecret) . ")\n";
else echo "ENV Secret: NULL\n";

// 3. Resolved Logic (matches AuthController)
$finalKey = null;
$finalSecret = null;

if ($gateway && !empty($gateway->credentials)) {
    $finalKey = $dbKey;
    $finalSecret = $dbSecret;
}
if (empty($finalKey) || empty($finalSecret)) {
    echo "Falling back to ENV...\n";
    $finalKey = $envKey;
    $finalSecret = $envSecret;
}

echo "--- Final Resolved Credentials ---\n";
echo "Key: " . ($finalKey ? substr($finalKey, 0, 8) . "..." : "NULL") . "\n";
echo "Secret: " . ($finalSecret ? "SET" : "NULL") . "\n";

if (empty($finalKey) || empty($finalSecret)) {
    echo "ERROR: Missing Credentials.\n";
    exit(1);
}

// 4. Test API Call
echo "--- Testing API Connection ---\n";

try {
    $response = Http::withBasicAuth($finalKey, $finalSecret)
        ->post('https://api.razorpay.com/v1/orders', [
            'amount' => 100, // 1 INR
            'currency' => 'INR',
            'receipt' => 'debug_' . time(),
        ]);

    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";

    if ($response->successful()) {
        echo "SUCCESS: API call worked. Order created.\n";
    } else {
        echo "FAILURE: API call failed.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
