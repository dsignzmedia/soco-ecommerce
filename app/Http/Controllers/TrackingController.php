<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtdcService;

class TrackingController extends Controller
{
    protected $dtdcService;

    public function __construct(DtdcService $dtdcService)
    {
        $this->dtdcService = $dtdcService;
    }

    public function index()
    {
        return view('tracking.show'); // Initial view with form
    }

    public function track(Request $request)
    {
        $request->validate([
            'awb' => 'required|string'
        ]);

        $trackingData = null;
        $error = null;

        try {
            $response = $this->dtdcService->trackShipment($request->awb);
            
            if (isset($response['success']) && $response['success']) {
                $trackingData = $response['data'];
            } else {
                $error = $response['message'] ?? 'Tracking info not found.';
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (isset($trackingData['raw']['strError'])) {
             $error = "API Error: " . $trackingData['raw']['strError'];
        }
        if (isset($trackingData['raw']['errorDetails'][0]['strError'])) {
             $error = "API Error: " . $trackingData['raw']['errorDetails'][0]['strError'];
        }

        return view('tracking.show', [
            'trackingData' => $trackingData,
            'awb' => $request->awb,
            'error' => $error
        ]);
    }
}
