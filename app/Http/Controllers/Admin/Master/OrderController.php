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

class OrderController extends Controller
{
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
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->where('school_id', $school))
            ->when($filters['grade'] ?? null, fn($query, $grade) => $query->where('grade', $grade))
            ->when($filters['category'] ?? null, fn($query, $category) => $query->where('category', $category))
            ->when($filters['product_type'] ?? null, fn($query, $type) => $query->where('product_type', 'like', $type . '%'))
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->where('order_status', $status))
            ->when($filters['payment_status'] ?? null, fn($query, $status) => $query->where('payment_status', $status))
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
        $data = $request->validate([
            'order_status' => ['required', 'string', 'in:order_placed,processing,packed,shipped,delivered'],
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

    public function invoiceView(Order $order): View
    {
        $order->load('school');
        return view('admin.orders.invoice', compact('order'));
    }

    public function invoiceDownload(Order $order)
    {
        try {
            $order->load('school');
            
            // Bypass container resolution and instantiate directly
            // We check for the core Dompdf class first (cleanest), then the wrapper
            if (class_exists(\Dompdf\Dompdf::class)) {
                 $dompdf = new \Dompdf\Dompdf();
                 $options = new \Dompdf\Options();
                 $options->set('isRemoteEnabled', true);
                 $options->set('defaultFont', 'sans-serif');
                 $dompdf->setOptions($options);
                 
                 // Render view to HTML
                 $html = view('admin.orders.invoice-pdf', compact('order'))->render();
                 $dompdf->loadHtml($html);
                 $dompdf->setPaper('A4', 'portrait');
                 $dompdf->render();
                 
                 return response()->streamDownload(function() use ($dompdf) {
                     echo $dompdf->output();
                 }, $order->order_number . '-invoice.pdf');
            } elseif (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                 $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice-pdf', compact('order'));
                 return $pdf->download($order->order_number . '-invoice.pdf');
            } else {
                 throw new \Exception("DomPDF classes not found. Please verify vendor libraries are uploaded.");
            }
        } catch (\Throwable $e) {
            return response("<h1>PDF Generation Failed</h1><pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>");
        }
    }
}
