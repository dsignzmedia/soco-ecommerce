<?php

namespace App\Traits;

use App\Models\Admin\Master\Order;
use App\Services\DtdcService;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

trait DtdcShipmentTrait
{
    /**
     * Cancel a shipment via DTDC API
     */
    public function cancelShipment(Request $request, Order $order)
    {
        if (! $order->tracking_number) {
            return back()->with('error', 'Order has no tracking number to cancel.');
        }

        try {
            /** @var DtdcService $dtdcService */
            $dtdcService = app(DtdcService::class);
            $response = $dtdcService->cancelShipment($order->tracking_number);

            if (isset($response['success']) && $response['success']) {
                $order->update(['order_status' => 'cancelled']);
                
                AuditLogger::record('order_override', $order, [
                    'action' => 'shipment_cancelled', 
                    'tracking_number' => $order->tracking_number
                ], 'Shipment Cancelled via DTDC');

                return back()->with('success', 'Shipment cancelled successfully via DTDC.');
            } else {
                return back()->with('error', 'Failed to cancel shipment: ' . ($response['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error cancelling shipment: ' . $e->getMessage());
        }
    }
}
