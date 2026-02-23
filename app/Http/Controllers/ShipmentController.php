<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtdcService;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    protected $dtdcService;

    public function __construct(DtdcService $dtdcService)
    {
        $this->dtdcService = $dtdcService;
    }

    public function create()
    {
        return view('shipment.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string',
            'phone' => 'required|numeric|digits:10',
            'address' => 'required|string',
            'pincode' => 'required|numeric|digits:6',
            'city' => 'required|string',
            'state' => 'required|string',
            'weight' => 'required|numeric',
            'declared_value' => 'required|numeric',
        ]);

        try {
            $data = [
                'environment' => $request->input('environment', 'production'),
                'name' => $request->receiver_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'city' => $request->city,
                'state' => $request->state,
                'weight' => $request->weight,
                'declared_value' => $request->declared_value,
                'reference_number' => 'REF-' . time() . '-' . rand(1000, 9999),
            ];

            $response = $this->dtdcService->createShipment($data);
            \Illuminate\Support\Facades\Log::info("DTDC  Response" . json_encode($response));

            // Parse Response correctly
            $isSuccess = false;
            $message = 'Unknown error';
            $responseDataItem = [];
            
            if (isset($response['data']) && is_array($response['data']) && count($response['data']) > 0) {
                 $responseDataItem = $response['data'][0];
                 $isSuccess = $responseDataItem['success'] ?? false;
                 $message = $responseDataItem['message'] ?? ($response['message'] ?? 'Unknown error');
            } else {
                 $message = $response['message'] ?? 'Invalid API Response';
            }

            // Store in DB
            try {
                $logData = [
                    'request_data' => $data,
                    'response_data' => $response,
                    'status' => $isSuccess ? 'success' : 'failure',
                    'reference_number' => $responseDataItem['reference_number'] ?? ($data['reference_number'] ?? null),
                    'awb' => $responseDataItem['awb'] ?? null,
                ];

                \App\Models\DtdcApiLog::create($logData);

            } catch (\Exception $e) {
                Log::error("Failed to log DTDC API response: " . $e->getMessage());
            }

            if ($isSuccess) {
                $ref = $responseDataItem['reference_number'] ?? 'N/A';
                $awb = $responseDataItem['awb'] ?? 'N/A';
                
                $env = $data['environment'] === 'staging' ? 'staging' : 'production';
                $downloadLink = route('label.generate', ['reference' => $ref]) . '?env=' . $env;
                
                return back()->with('success', "Shipment Created! Reference: $ref, AWB: $awb. <a href='$downloadLink' target='_blank' class='underline font-bold text-blue-600'>Download Label</a>");
            }

            return back()->with('error', 'Failed to create shipment: ' . $message);

        } catch (\Exception $e) {
            Log::error("Shipment Create Error: " . $e->getMessage());
            return back()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }
}
