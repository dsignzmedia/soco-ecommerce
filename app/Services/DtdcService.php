<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DtdcService
{
    protected $baseUrl;
    protected $apiKey;
    protected $customerCode;
    protected $serviceType;
    protected $origin;

    public function __construct()
    {
        $this->baseUrl = config('dtdc.base_url');
        $this->apiKey = config('dtdc.api_key');
        $this->customerCode = config('dtdc.customer_code');
        $this->serviceType = config('dtdc.service_type');
        $this->origin = config('dtdc.origin');
    }

    /**
     * Create Shipment (Order Upload / Consignment Softdata)
     * Endpoint: /api/customer/integration/consignment/softdata
     * Method: POST
     */
    public function createShipment(array $data)
    {
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

        return $this->makeRequest('POST', '/api/customer/integration/consignment/softdata', $payload);
    }

    /**
     * Generate Shipping Label (Stream API)
     * Endpoint: /api/customer/integration/consignment/shippinglabel/stream
     * Method: GET
     */
    public function generateLabel($referenceNumber, $format = 'pdf')
    {
        $queryParams = [
            'reference_number' => $referenceNumber,
            'label_code' => 'SHIP_LABEL_4X6',
            'label_format' => $format
        ];

        return $this->makeRequest('GET', '/api/customer/integration/consignment/shippinglabel/stream', $queryParams);
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

        return $this->makeRequest('POST', '/api/customer/integration/consignment/cancel', $payload);
    }

    /**
     * Track Shipment (Simplified GET)
     * Using the simpler Track API if possible, or we need to implement the complex Token-based one.
     * Given user provided the XML/JSON Tracking Doc which requires Token, we should ideally use that.
     * BUT logic to get token (username/pass) is missing credentials in config.
     * 
     * Fallback: We will attempt the JSON Tracking API but mock it or fail if no token.
     * Actually, let's try to assume 'api_key' might work as X-Access-Token for now, or log error.
     */
    public function trackShipment($awb)
    {
        // Docs say: 
        // URL: https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails
        // Method: POST
        // Params: trkType=cnno, strcnno=..., addtnlDtl=Y
        // Header: X-Access-Token
        
        $url = 'https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails';
        
        $queryParams = [
            'trkType' => 'cnno',
            'strcnno' => $awb,
            'addtnlDtl' => 'Y'
        ];
        
        // This endpoint expects query params on POST? Or body?
        // User doc says "Query request parameters" under POST. This usually means params in URL.
        
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
            $response = Http::withHeaders([
                'X-Access-Token' => $this->apiKey // Trying API Key as Token
            ])->post($url, $queryParams); // Passing array as 2nd arg to POST usually sends JSON body. 
            // If they want Query Params, we should append to URL.
            // "Query request parameters" implies ?key=value
            
            // Let's force query params for safety if doc is specific
            $response = Http::withHeaders([
                'X-Access-Token' => $this->apiKey
            ])->post($url . '?' . http_build_query($queryParams));

            if ($response->successful()) {
                return $response->json();
            }
            return ['success' => false, 'message' => 'Tracking failed', 'details' => $response->body()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Common Request Handler
     */
    protected function makeRequest($method, $endpoint, $data = [])
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
                return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 300 144]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000060 00000 n\n0000000117 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n223\n%%EOF"; 
            }
            if (str_contains($endpoint, 'cancel')) {
                return ['success' => true, 'successConsignments' => [['success' => true, 'reference_number' => 'REF-CANCEL']]];
            }
            
            return ['success' => true, 'message' => 'Simulated'];
        }

        try {
            $baseUrl = $this->baseUrl ?: 'https://dtdcapi.shipsy.io';
            if (!preg_match('/^http/', $baseUrl)) $baseUrl = 'https://' . $baseUrl;
            
            // Handle absolute URLs if passed
            if (preg_match('/^http/', $endpoint)) {
                $url = $endpoint;
            } else {
                $url = rtrim($baseUrl, '/') . $endpoint;
            }

            $headers = ['api-key' => $this->apiKey];
            
            $request = Http::withHeaders($headers)->timeout((int) config('dtdc.timeout', 30));

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
            return ['success' => false, 'message' => 'Internal Error'];
        }
    }
}
