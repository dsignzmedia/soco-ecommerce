<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\School;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\DtdcShipmentTrait;

class OrderController extends Controller
{
    use DtdcShipmentTrait;
    public function index(Request $request): View
    {
        $filters = $request->only([
            'school_id',
            'grade',
            'category',
            'product_type',
            'order_status',
            'payment_status',
            'date_from',
            'date_to',
            'order_number',
        ]);

        $orders = Order::with(['school', 'product'])
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->whereIn('school_id', (array)$school))
            ->when($filters['grade'] ?? null, fn($query, $grade) => $query->whereIn('grade', (array)$grade))
            ->when($filters['category'] ?? null, fn($query, $category) => $query->whereIn('category', (array)$category))
            ->when($filters['product_type'] ?? null, function($query, $types) {
                $types = (array)$types;
                return $query->where(function($q) use ($types) {
                    foreach($types as $type) {
                        $q->orWhere('product_type', 'like', $type . '%');
                    }
                });
            })
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->whereIn('order_status', (array)$status))
            ->when($filters['payment_status'] ?? null, fn($query, $status) => $query->whereIn('payment_status', (array)$status))
            ->when($filters['order_number'] ?? null, fn($query, $number) => $query->where('order_number', 'like', '%' . $number . '%'))
            ->when($filters['date_from'] ?? null, fn($query, $from) => $query->whereDate('order_date', '>=', Carbon::parse($from)))
            ->when($filters['date_to'] ?? null, fn($query, $to) => $query->whereDate('order_date', '<=', Carbon::parse($to)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $grades = Order::select('grade')->whereNotNull('grade')->distinct()->orderBy('grade')->pluck('grade');
        $categories = Order::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $productTypes = Order::select('product_type')->whereNotNull('product_type')->distinct()->orderBy('product_type')->pluck('product_type');

        return view('admin.orders.index', compact('orders', 'schools', 'grades', 'categories', 'productTypes', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load('school');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($request->input('order_status') === 'cancelled') {
            return $this->cancelShipment($request, $order);
        }

        $data = $request->validate([
            'order_status' => ['required', 'string', 'in:order_placed,processing,packed,shipped,delivered,cancelled'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        $original = $order->only(['order_status', 'payment_status', 'tracking_number']);
        $previousOrderStatus = $order->order_status;
        $previousPaymentStatus = $order->payment_status;
        
        $order->update(array_filter($data, fn($value) => $value !== null));

        $changes = [];
        foreach ($original as $field => $before) {
            $after = $order->{$field};
            if ($before != $after) {
                $changes[$field] = [
                    'before' => $before,
                    'after' => $after,
                ];
            }
        }

        if (! empty($changes)) {
            AuditLogger::record(
                'order_override',
                $order,
                [
                    'order_number' => $order->order_number,
                    'changes' => $changes,
                ],
                'Order override / status update'
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
            
            // Send Payment Status Update Email if payment status changed
            if (isset($changes['payment_status']) && !empty($order->customer_email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\PaymentStatusMail($order, $previousPaymentStatus));
                    \Illuminate\Support\Facades\Log::info("Payment status update email sent to {$order->customer_email} for order {$order->order_number}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send payment status update email: " . $e->getMessage());
                }
            }
        }

        return back()->with('status', 'Order updated successfully.');
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'school_id',
            'grade',
            'category',
            'product_type',
            'order_status',
            'payment_status',
            'date_from',
            'date_to',
            'order_number',
        ]);
        
        $type = $request->input('export_type', 'excel');
        
        // Build query with filters
        $query = Order::with(['school', 'product'])->latest();
        
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }
        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['product_type'])) {
            $query->where('product_type', 'like', $filters['product_type'] . '%');
        }
        if (!empty($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        }
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }
        if (!empty($filters['order_number'])) {
            $query->where('order_number', 'like', '%' . $filters['order_number'] . '%');
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('order_date', '>=', \Carbon\Carbon::parse($filters['date_from']));
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('order_date', '<=', \Carbon\Carbon::parse($filters['date_to']));
        }
        
        $orders = $query->get();
        
        switch (strtolower($type)) {
            case 'csv':
            case 'excel':
                return $this->downloadOrdersCsv($orders, $type);
            
            case 'pdf':
                return $this->downloadOrdersPdf($orders);
            
            default:
                return redirect()->route('master.admin.orders.index')
                    ->with('error', 'Invalid export type.');
        }
    }
    
    protected function downloadOrdersCsv($orders, $type)
    {
        $filename = 'orders-' . date('Y-m-d') . '.csv';
        $contentType = 'text/csv';
        
        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers - Priority columns first for business use
            fputcsv($handle, [
                'Order Number',
                'Order Date',
                'Customer Name',
                'Customer Phone',
                'Customer Email',
                'Item Name',
                'Size',
                'Qty',
                'Total Amount (Rs)',
                'Shipping Cost (Rs)',
                'Payment Status',
                'Order Status',
                'School',
                'Student Name',
                'Grade',
                'Category',
                'Tracking Number',
            ]);
            
            // Data rows - Clean formatting
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->order_date ? $order->order_date->format('Y-m-d') : ($order->created_at ? $order->created_at->format('Y-m-d') : ''),
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_email,
                    $order->item_name,
                    $order->size,
                    $order->quantity,
                    number_format($order->total_amount, 2),
                    number_format($order->shipping_cost, 2),
                    ucfirst(str_replace('_', ' ', $order->payment_status)),
                    ucfirst(str_replace('_', ' ', $order->order_status)),
                    $order->school ? $order->school->name : '',
                    $order->student_name,
                    $order->grade ?? '',
                    $order->category ?? '',
                    $order->tracking_number ?? '',
                ]);
            }
            
            fclose($handle);
        }, $filename, ['Content-Type' => $contentType]);
    }
    
    protected function downloadOrdersPdf($orders)
    {
        // Simple PDF generation using DomPDF
        $html = view('admin.orders.export-pdf', compact('orders'))->render();
        
        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'sans-serif');
            $dompdf->setOptions($options);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            return response()->streamDownload(function() use ($dompdf) {
                echo $dompdf->output();
            }, 'orders-' . date('Y-m-d') . '.pdf');
        }
        
        return redirect()->route('master.admin.orders.index')
            ->with('error', 'PDF generation not available.');
    }

    public function invoiceView(Order $order): View
    {
        $order->load('school');
        
        // Extract transaction prefix (SOCO-USERID-TIMESTAMP) from order number
        $parts = explode('-', $order->order_number);
        if (count($parts) >= 3) {
            $transactionPrefix = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            $orders = Order::where('order_number', 'like', $transactionPrefix . '%')
                ->get();
        } else {
            // Fallback for short format or missing prefix
            $orders = collect([$order]);
        }
            
        return view('admin.orders.invoice', compact('order', 'orders'));
    }

    public function invoiceDownload(Order $order)
    {
        try {
             $order->load('school');
             
             // Extract transaction prefix (SOCO-USERID-TIMESTAMP) from order number
             $parts = explode('-', $order->order_number);
             if (count($parts) >= 3) {
                 $transactionPrefix = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
                 $orders = Order::where('order_number', 'like', $transactionPrefix . '%')
                     ->get();
             } else {
                 $orders = collect([$order]);
             }
             
             // Bypass container resolution and instantiate directly
             // We check for the core Dompdf class first (cleanest), then the wrapper
             if (class_exists(\Dompdf\Dompdf::class)) {
                  $dompdf = new \Dompdf\Dompdf();
                  $options = new \Dompdf\Options();
                  $options->set('isRemoteEnabled', true);    
                  $options->set('defaultFont', 'sans-serif');
                  $dompdf->setOptions($options);
                  
                  // Render view to HTML
                  $html = view('admin.orders.invoice-pdf', compact('order', 'orders'))->render();
                  $dompdf->loadHtml($html);
                  $dompdf->setPaper('A4', 'portrait');
                  $dompdf->render();
                  
                  return response()->streamDownload(function() use ($dompdf) {
                      echo $dompdf->output();
                  }, $order->order_number . '-invoice.pdf');
             } elseif (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                  $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice-pdf', compact('order', 'orders'));
                  return $pdf->download($order->order_number . '-invoice.pdf');
            } else {
                 throw new \Exception("DomPDF classes not found. Please verify vendor libraries are uploaded.");
            }
        } catch (\Throwable $e) {
            return response("<h1>PDF Generation Failed</h1><pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>");
        }
    }
}
