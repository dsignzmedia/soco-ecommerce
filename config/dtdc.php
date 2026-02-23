<?php

return [
    'api_key' => env('DTDC_API_KEY'),
    'special_key' => env('DTDC_SPECIAL_KEY'), // For Label/Cancel
    'customer_code' => env('DTDC_CUSTOMER_CODE'),
    'access_token' => env('DTDC_ACCESS_TOKEN'),
    'tracking_username' => env('DTDC_TRACKING_USERNAME'),
    'base_url' => env('DTDC_BASE_URL', 'https://dtdcapi.shipsy.io'),
    'staging_url' => env('DTDC_BASE_URL_STAGE', 'https://alphademodashboardapi.shipsy.io'),
    'production_url' => env('DTDC_BASE_URL', 'https://dtdcapi.shipsy.io'),
    'service_type' => env('DTDC_SERVICE_TYPE', 'B2C Smart Express'),
    'test_mode' => env('DTDC_TEST_MODE', false),
    'timeout' => 30,
    
    // Origin Details for Shipments (Required by API)
    'origin' => [
        'name' => env('DTDC_ORIGIN_NAME', 'The Skool Store'),
        'phone' => env('DTDC_ORIGIN_PHONE', '9876543210'), 
        'address' => env('DTDC_ORIGIN_ADDRESS', 'Warehouse 1'),
        'city' => env('DTDC_ORIGIN_CITY', 'Chennai'),
        'state' => env('DTDC_ORIGIN_STATE', 'Tamil Nadu'),
        'pincode' => env('DTDC_ORIGIN_PINCODE', '600001'),
    ],
];
