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

            if (isset($response['success']) && $response['success']) {
                return back()->with('success', 'Shipment cancelled successfully.');
            }

            return back()->with('error', 'Cancellation failed: ' . ($response['message'] ?? 'Unknown API error'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
