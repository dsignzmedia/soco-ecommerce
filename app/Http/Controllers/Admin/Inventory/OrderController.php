<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\School;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\DtdcShipmentTrait;

class OrderController extends Controller
{
    use DtdcShipmentTrait;
    public function index(Request $request): View
    {
        $filters = $request->only([
            'school_id',
            'order_status',
            'date_from',
            'date_to',
            'order_number',
        ]);

        $orders = Order::with(['school', 'product'])
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->where('school_id', $school))
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->where('order_status', $status))
            ->when($filters['order_number'] ?? null, fn($query, $number) => $query->where('order_number', 'like', '%' . $number . '%'))
            ->when($filters['date_from'] ?? null, fn($query, $from) => $query->whereDate('order_date', '>=', Carbon::parse($from)))
            ->when($filters['date_to'] ?? null, fn($query, $to) => $query->whereDate('order_date', '<=', Carbon::parse($to)))
            ->orderByDesc('order_date')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        
        // Statuses relevant to inventory - ordered by workflow sequence
        $statuses = [
            'order_placed' => 'Order Placed',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        return view('inventoryadmin.orders.index', compact('orders', 'schools', 'statuses', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load('school');
        return view('inventoryadmin.orders.show', compact('order'));
    }

    public function shipping(Request $request): View
    {
        $filters = $request->only(['school_id', 'q']);

        $orders = Order::with('school')
            ->where('order_status', 'ready_to_ship')
            ->when($filters['school_id'] ?? null, fn($query, $schoolId) => $query->where('school_id', $schoolId))
            ->when($filters['q'] ?? null, fn($query, $search) => $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            }))
            ->orderBy('order_date')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();

        return view('inventoryadmin.orders.shipping', compact('orders', 'schools', 'filters'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($request->input('order_status') === 'cancelled') {
            return $this->cancelShipment($request, $order);
        }

        $data = $request->validate([
            'order_status' => ['required', 'string', 'in:order_placed,processing,packed,shipped,delivered,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'courier_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $original = $order->only(['order_status', 'tracking_number', 'courier_name', 'notes']);
        $previousOrderStatus = $order->order_status;
        $order->update(array_filter($data, fn($value) => $value !== null));

        // Audit Log logic (simplified for now, reusing existing if available or skipping)
        try {
            $changes = [];
            foreach ($original as $field => $before) {
                $after = $order->{$field};
                if ($before != $after) {
                    $changes[$field] = ['before' => $before, 'after' => $after];
                }
            }

            if (!empty($changes)) {
                AuditLogger::record(
                    'inventory_update',
                    $order,
                    ['order_number' => $order->order_number, 'changes' => $changes],
                    'Inventory Admin status update'
                );
                
                // Send Order Status Update Email if order status changed (including delivered status)
                if (isset($changes['order_status']) && !empty($order->customer_email)) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderStatusUpdateMail($order, $previousOrderStatus));
                        \Illuminate\Support\Facades\Log::info("Order status update email sent to {$order->customer_email} for order {$order->order_number} - Status: {$order->order_status}");
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send order status update email for order {$order->order_number}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail logging if AuditLogger has issues in this context
        }

        return back()->with('status', 'Order updated successfully.');
    }
    
    public function packingSlip(Order $order)
    {
        $order->load('school');
        // Reuse the PDF generation logic from Master Admin but tailored for Packing Slip
        $lines = [
            'PACKING SLIP',
            'Order #' . $order->order_number,
            'Date: ' . optional($order->order_date)->format('d M Y'),
            '----------------------------------------',
            'Ship To:',
            $order->customer_name,
            $order->customer_address,
            'Phone: ' . $order->customer_phone,
            '----------------------------------------',
            'Items:',
            $order->item_name . ' (' . $order->size . ') x ' . $order->quantity,
            'Category: ' . $order->category,
            'School: ' . optional($order->school)->name,
            'Student: ' . $order->student_name . ' (' . $order->grade . ')',
            '----------------------------------------',
            'Notes: ' . ($order->notes ?? 'None'),
        ];

        $pdf = $this->buildSimplePdf($lines);

        return response()->streamDownload(fn () => print($pdf), $order->order_number . '-packing-slip.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function printLabel(Order $order)
    {
        if (!$order->tracking_number) {
            return back()->with('error', 'Order has no tracking number. Cannot generate label.');
        }

        $filename = "labels/{$order->order_number}.pdf";
        
        // Return cached label if exists
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filename)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($filename);
        }

        try {
            \Illuminate\Support\Facades\Log::info("Print Label Requested for Order: {$order->order_number}");

            /** @var \App\Services\DtdcService $dtdcService */
            $dtdcService = app(\App\Services\DtdcService::class);
            
            // Fetch label from DTDC API using reference number
            // IMPORTANT: The shipment was created with the Group Order ID (without the item suffix index).
            // We must strip the suffix to get the correct reference number used for shipment creation.
            $referenceNumber = \Illuminate\Support\Str::beforeLast($order->order_number, '-');
            $response = $dtdcService->generateLabel($referenceNumber);

            \Illuminate\Support\Facades\Log::info("DTDC Label Response Type: " . gettype($response));

            // Handle API errors (if response is array with success=false)
            if (is_array($response)) {
                 \Illuminate\Support\Facades\Log::error("DTDC Label API Error Response", $response);
                 if (isset($response['success']) && !$response['success']) {
                     return back()->with('error', 'DTDC API Error: ' . ($response['message'] ?? 'Unknown error'));
                 }
                 // If it's an array but not success=false, it might be unexpected JSON
                 return back()->with('error', 'Unexpected response from DTDC API.');
            }

            // If response is a string, check if it's a PDF
            if (is_string($response)) {
                $isPdf = str_starts_with($response, '%PDF');
                \Illuminate\Support\Facades\Log::info("DTDC Label Response is String. Is PDF? " . ($isPdf ? 'Yes' : 'No'));
                
                if ($isPdf) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response);
                    \Illuminate\Support\Facades\Log::info("Label saved to storage: {$filename}");
                    return \Illuminate\Support\Facades\Storage::disk('public')->download($filename);
                }
                
                // Log non-PDF string content (start of it)
                \Illuminate\Support\Facades\Log::warning("DTDC returned non-PDF string: " . substr($response, 0, 100));
            }

            // Fallback for mocks or non-PDF strings
            if (config('dtdc.test_mode')) {
                 return back()->with('error', 'Test Mode: DTDC Service returned mock data ("' . substr(is_string($response) ? $response : json_encode($response), 0, 20) . '...") instead of a real PDF.');
            }

            return back()->with('error', 'Invalid label format received from DTDC.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Label Generation Error: ' . $e->getMessage());
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}
