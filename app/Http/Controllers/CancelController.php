<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtdcService;

class CancelController extends Controller
{
    protected $dtdcService;

    public function __construct(DtdcService $dtdcService)
    {
        $this->dtdcService = $dtdcService;
    }

    public function index()
    {
        return view('cancel.form');
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'awb' => 'required|string',
        ]);

        try {
            $response = $this->dtdcService->cancelShipment($request->awb);

            // 1. Check for top-level success
            if ((isset($response['success']) && $response['success']) || (isset($response['status']) && $response['status'] === 'OK')) {
                // DTDC sometimes returns success=true but lists errors inside 'data' or lists successful AWBs
                // Example: { success: true, successConsignments: [...], failureConsignments: [...] }
                
                $message = 'Shipment cancelled successfully.';
                
                // Check if our AWB is in failure list
                if (isset($response['failureConsignments']) && is_array($response['failureConsignments'])) {
                    foreach ($response['failureConsignments'] as $fail) {
                        if (($fail['awb'] ?? '') == $request->awb) {
                             return back()->with('error', 'Cancellation Failed: ' . ($fail['message'] ?? 'Unknown Reason'));
                        }
                    }
                }
                
                return back()->with('success', $message);
            }
            
            // 2. Fallback Error
            $msg = $response['message'] ?? 'Unknown API error';
            if (isset($response['data']['message'])) {
                 $msg = $response['data']['message'];
            }

            return back()->with('error', 'Cancellation failed: ' . $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
