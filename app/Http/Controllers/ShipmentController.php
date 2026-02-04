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
            'weight' => 'required|numeric',
            'declared_value' => 'required|numeric',
        ]);

        try {
            $data = [
                'name' => $request->receiver_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'weight' => $request->weight,
                'declared_value' => $request->declared_value,
            ];

            $response = $this->dtdcService->createShipment($data);

            if (isset($response['success']) && $response['success']) {
                $ref = $response['data']['reference_number'] ?? 'N/A';
                $awb = $response['data']['awb'] ?? 'N/A';
                
                return back()->with('success', "Shipment Created! Reference: $ref, AWB: $awb");
            }

            return back()->with('error', 'Failed to create shipment: ' . ($response['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error("Shipment Create Error: " . $e->getMessage());
            return back()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }
}
