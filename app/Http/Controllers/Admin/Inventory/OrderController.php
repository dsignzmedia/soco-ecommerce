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
            'payment_status',
            'product_type',
            'date_from',
            'date_to',
            'order_number',
        ]);

        $orders = Order::with(['school', 'product'])
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->whereIn('school_id', (array)$school))
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->whereIn('order_status', (array)$status))
            ->when($filters['payment_status'] ?? null, fn($query, $status) => $query->whereIn('payment_status', (array)$status))
            ->when($filters['product_type'] ?? null, fn($query, $type) => $query->whereIn('product_type', (array)$type))
            ->when($filters['order_number'] ?? null, fn($query, $number) => $query->where('order_number', 'like', '%' . $number . '%'))
            ->when($filters['date_from'] ?? null, fn($query, $from) => $query->whereDate('order_date', '>=', Carbon::parse($from)))
            ->when($filters['date_to'] ?? null, fn($query, $to) => $query->whereDate('order_date', '<=', Carbon::parse($to)))
            ->orderByDesc('order_date')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $productTypes = Order::whereNotNull('product_type')->distinct()->pluck('product_type')->sort();
        
        // Statuses relevant to inventory - ordered by workflow sequence
        $statuses = [
            'order_placed' => 'Order Placed',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        return view('inventoryadmin.orders.index', compact('orders', 'schools', 'statuses', 'filters', 'productTypes'));
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
        
        // Items - for now just the single order row? 
        // In Master Admin we consolidated by transaction, but packing slips are usually per item or per shipment.
        // Let's stick to transaction prefix grouping for consistency if it's multiple items in one order.
        $parts = explode('-', $order->order_number);
        if (count($parts) >= 3) {
            $transactionPrefix = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            $orders = Order::where('order_number', 'like', $transactionPrefix . '%')->get();
        } else {
            $orders = collect([$order]);
        }

        try {
            if (class_exists(\Dompdf\Dompdf::class)) {
                $dompdf = new \Dompdf\Dompdf();
                $options = new \Dompdf\Options();
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'sans-serif');
                $dompdf->setOptions($options);
                
                // We'll reuse the master layout or a clean one if available. 
                // For now, let's create a minimal HTML view for the packing slip or use the master admin's invoice-pdf but labeled as Packing Slip?
                // Actually the user just wanted to fix the error. The original code was trying to call buildSimplePdf.
                $html = view('inventoryadmin.orders.packing-slip-pdf', compact('order', 'orders'))->render();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                return response()->streamDownload(fn () => print($dompdf->output()), $order->order_number . '-packing-slip.pdf', [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Packing slip generation error: " . $e->getMessage());
            return back()->with('error', 'Failed to generate packing slip.');
        }

        return back()->with('error', 'PDF generation not available.');
    }

    public function invoiceView(Order $order): View
    {
        $order->load('school');
        
        // Extract transaction prefix (SOCO-USERID-TIMESTAMP) from order number
        $parts = explode('-', $order->order_number);
        if (count($parts) >= 3) {
            $transactionPrefix = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            // Include ALL items in the transaction, but prioritize paid ones if they exist.
            // Actually, we should include the items even if not paid for tax invoice preview purposes.
            $orders = Order::where('order_number', 'like', $transactionPrefix . '%')
                ->get();
        } else {
            $orders = collect([$order]);
        }
            
        // Use the dedicated inventory invoice view
        return view('inventoryadmin.orders.invoice', compact('order', 'orders'));
    }

    public function invoiceDownload(Order $order)
    {
        try {
             $order->load('school');
             
             $parts = explode('-', $order->order_number);
             if (count($parts) >= 3) {
                 $transactionPrefix = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
                 $orders = Order::where('order_number', 'like', $transactionPrefix . '%')
                     ->get();
             } else {
                 $orders = collect([$order]);
             }
             
             if (class_exists(\Dompdf\Dompdf::class)) {
                  $dompdf = new \Dompdf\Dompdf();
                  $options = new \Dompdf\Options();
                  $options->set('isRemoteEnabled', true);    
                  $options->set('defaultFont', 'sans-serif');
                  $dompdf->setOptions($options);
                  
                  $html = view('admin.orders.invoice-pdf', compact('order', 'orders'))->render();
                  $dompdf->loadHtml($html);
                  $dompdf->setPaper('A4', 'portrait');
                  $dompdf->render();
                  
                  return response()->streamDownload(fn() => print($dompdf->output()), 'Invoice-' . $order->order_number . '.pdf');
             }
        } catch (\Exception $e) {
             \Log::error("Invoice download error: " . $e->getMessage());
             return back()->with('error', 'Failed to generate invoice.');
        }
        
        return back()->with('error', 'PDF generation not available.');
    }

    /**
     * Helper to build a simple PDF from lines (Fallback or simplified version)
     */
    protected function buildSimplePdf(array $lines)
    {
        $html = "<html><body style='font-family:sans-serif;'>";
        foreach ($lines as $line) {
            $html .= "<div>" . e($line) . "</div>";
        }
        $html .= "</body></html>";

        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return $dompdf->output();
        }
        return null;
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
