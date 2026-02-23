<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DtdcService
{
    protected $baseUrl;
    protected $specialKey;

    public function __construct()
    {
        $this->baseUrl = config('dtdc.base_url');
        $this->apiKey = config('dtdc.api_key');
        $this->specialKey = config('dtdc.special_key'); // Load Special Key
        $this->customerCode = config('dtdc.customer_code');
        $this->serviceType = config('dtdc.service_type');
        $this->origin = config('dtdc.origin');
        $this->accessToken = config('dtdc.access_token');
        $this->trackingUsername = config('dtdc.tracking_username');
    }

    /**
     * Create Shipment (Order Upload / Consignment Softdata)
     * Endpoint: /api/customer/integration/consignment/softdata
     * Method: POST
     */
    public function createShipment(array $data)
    {
        // 0. Determine Environment
        $env = $data['environment'] ?? 'production';
        $baseUrl = ($env === 'staging') ? config('dtdc.staging_url') : config('dtdc.production_url');

        // 1. Prepare Consignment Object
        $consignment = [
            "customer_code" => $this->customerCode,
            "service_type_id" => $this->serviceType,
            "load_type" => "NON-DOCUMENT",
            "description" => $data['description'] ?? 'School Supplies',
            "dimension_unit" => "cm",
            "length" => "10.0",
            "width" => "10.0",
            "height" => "10.0",
            "weight_unit" => "kg",
            "weight" => (string) ($data['weight'] ?? "0.5"),
            "declared_value" => (string) ($data['declared_value'] ?? "500"),
            "num_pieces" => "1",
            "origin_details" => [
                "name" => $this->origin['name'],
                "phone" => $this->origin['phone'],
                "address_line_1" => $this->origin['address'],
                "address_line_2" => "",
                "pincode" => $this->origin['pincode'],
                "city" => $this->origin['city'],
                "state" => $this->origin['state']
            ],
            "destination_details" => [
                "name" => $data['name'],
                "phone" => $data['phone'],
                "address_line_1" => $data['address'],
                "address_line_2" => "",
                "pincode" => $data['pincode'],
                "city" => $data['city'],
                "state" => $data['state']
            ],
            "customer_reference_number" => (string) $data['reference_number'],
            "is_risk_surcharge_applicable" => "false",
            "commodity_id" => "99"
        ];

        $payload = [
            "consignments" => [$consignment]
        ];

        return $this->makeRequest('POST', '/api/customer/integration/consignment/softdata', $payload, $baseUrl);
    }

    /**
     * Generate Shipping Label (Stream API)
     * Endpoint: /api/customer/integration/consignment/shippinglabel/stream
     * Method: GET
     */
    public function generateLabel($referenceNumber, $format = 'pdf', $env = 'production')
    {
        $queryParams = [
            'reference_number' => $referenceNumber,
            'label_code' => 'SHIP_LABEL_4X6',
            'label_format' => $format,
            'customer_code' => $this->customerCode
        ];

        $baseUrl = ($env === 'staging') ? config('dtdc.staging_url') : config('dtdc.production_url');

        // Use SPECIAL KEY for Label Generation
        return $this->makeRequest('GET', '/api/customer/integration/consignment/shippinglabel/stream', $queryParams, $baseUrl, [
            'api-key' => $this->specialKey, // Override API Key
        ]);
    }

    /**
     * Cancel Shipment
     * Endpoint: /api/customer/integration/consignment/cancel
     * Method: POST
     */
    public function cancelShipment($awb)
    {
        $payload = [
            "AWBNo" => [$awb], 
            "customerCode" => $this->customerCode
        ];

        // Use SPECIAL KEY for Cancellation
        return $this->makeRequest('POST', '/api/customer/integration/consignment/cancel', $payload, null, [
            'api-key' => $this->specialKey,
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Track Shipment (Simplified GET)
     * Uses Token Based Authentication
     */
    public function trackShipment($awb)
    {
        // URL: https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails
        // Method: POST/GET (Query parameters)
        
        $url = 'https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails';
        
        $queryParams = [
            'trkType' => 'cnno',
            'strcnno' => $awb,
            'addtnlDtl' => 'Y'
        ];
        
        if (config('dtdc.test_mode')) {
             return [
                'success' => true,
                'trackHeader' => [
                    'strShipmentNo' => $awb,
                    'strStatus' => 'In Transit (Simulated)',
                    'strStatusTransOn' => date('dmY'),
                    'strStatusTransTime' => date('Hi')
                ],
                'trackDetails' => [
                    [
                        'strAction' => 'In Transit',
                        'strOrigin' => 'Origin Hub',
                        'strDestination' => 'Destination Hub',
                        'strActionDate' => date('dmY'),
                        'strActionTime' => date('Hi')
                    ]
                ]
            ];
        }

        try {
            // Use X-Access-Token from config
            $token = $this->accessToken;
            if (!$token) {
                 // Fallback to API Key if token not set (though unlikley to work for this specific endpoint)
                 $token = $this->apiKey; 
            }

            // Force query params for this endpoint as per DTDC docs
            $response = Http::withHeaders([
                'X-Access-Token' => $token
            ])
            ->withoutVerifying() // Fix SSL for local
            ->get($url, $queryParams); // Attempt GET first as it's cleaner for query params

            if ($response->failed() && $response->status() == 405) {
                // If GET fails, try POST with JSON body
                 $response = Http::withHeaders([
                    'X-Access-Token' => $token,
                    'Content-Type' => 'application/json'
                ])
                ->withoutVerifying()
                ->post($url, $queryParams); // Send as JSON body
            }

            if ($response->successful()) {
                $json = $response->json();
                
                // Map DTDC response to our view structure
                $status = 'Unknown';
                $history = [];
                
                if (isset($json['trackHeader'])) {
                    $status = $json['trackHeader']['strStatus'] ?? 'Unknown';
                    // Add latest status to history as well
                    $history[] = [
                        'status' => $status,
                        'time' => ($json['trackHeader']['strStatusTransDate'] ?? '') . ' ' . ($json['trackHeader']['strStatusTransTime'] ?? '')
                    ];
                }

                if (isset($json['trackDetails']) && is_array($json['trackDetails'])) {
                    foreach ($json['trackDetails'] as $detail) {
                        $history[] = [
                            'status' => $detail['strAction'] ?? ($detail['strStatus'] ?? 'Update'),
                            'time' => ($detail['strActionDate'] ?? '') . ' ' . ($detail['strActionTime'] ?? '') . ' ' . ($detail['strOrigin'] ?? '')
                        ];
                    }
                }

                return [
                    'success' => true,
                    'data' => [
                        'status' => $status,
                        'awb' => $awb,
                        'history' => $history,
                        'raw' => $json
                    ]
                ];
            }
            return ['success' => false, 'message' => 'Tracking failed', 'details' => $response->body()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Common Request Handler
     */
    protected function makeRequest($method, $endpoint, $data = [], $baseUrlOverride = null, $customHeaders = [])
    {
        if (config('dtdc.test_mode')) {
            Log::info("DTDC TEST MODE: {$method} {$endpoint}", $data);
            
            // Simulation Logic
            if (str_contains($endpoint, 'softdata')) {
                return [
                    'success' => true, 
                    'data' => [[
                        'success' => true, 
                        'reference_number' => 'REF-' . time(), 
                        'awb' => 'AWB-TEST-' . rand(10000, 99999)
                    ]]
                ];
            }
            if (str_contains($endpoint, 'shippinglabel')) {
                // If PDF requested
                if (($data['label_format'] ?? '') == 'base64') {
                     return ['referenceNumber' => $data['reference_number'] ?? 'UNK', 'label' => base64_encode('Dummy Label')];
                }
                // Return valid minimal PDF for testing
                // Return valid PDF for testing: "DTDC TEST LABEL"
                return "%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 300 200] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>
endobj
4 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
5 0 obj
<< /Length 56 >>
stream
BT
/F1 24 Tf
50 100 Td
(DTDC TEST LABEL) Tj
ET
endstream
endobj
xref
0 6
0000000000 65535 f
0000000010 00000 n
0000000060 00000 n
0000000117 00000 n
0000000257 00000 n
0000000329 00000 n
trailer
<< /Size 6 /Root 1 0 R >>
startxref
436
%%EOF"; 
            }
            if (str_contains($endpoint, 'cancel')) {
                return ['success' => true, 'successConsignments' => [['success' => true, 'reference_number' => 'REF-CANCEL']]];
            }
            
            return ['success' => true, 'message' => 'Simulated'];
        }

        try {
            // Determine Base URL: Override > Config Base > Hardcoded Default
            $baseUrl = $baseUrlOverride ?: ($this->baseUrl ?: 'https://dtdcapi.shipsy.io');
            if (!preg_match('/^http/', $baseUrl)) $baseUrl = 'https://' . $baseUrl;
            
            
            // Handle absolute URLs if passed
            if (preg_match('/^http/', $endpoint)) {
                $url = $endpoint;
            } else {
                $url = rtrim($baseUrl, '/') . $endpoint;
            }

            $headers = array_merge(['api-key' => $this->apiKey], $customHeaders);
            
            $request = Http::withHeaders($headers)
                        ->withoutVerifying()
                        ->timeout((int) config('dtdc.timeout', 30));

            if ($method === 'GET') {
                $response = $request->get($url, $data);
            } else {
                $response = $request->post($url, $data);
            }

            if ($response->failed()) {
                Log::error("DTDC API Error: {$endpoint}", ['data' => $data, 'response' => $response->body()]);
                return ['success' => false, 'message' => 'Request Failed: ' . $response->reason()];
            }
            
            // Handle Stream/PDF content
            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'pdf') || str_contains($contentType, 'stream')) {
                return $response->body();
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error("DTDC Service Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Internal Error: ' . $e->getMessage()];
        }
    }
}
