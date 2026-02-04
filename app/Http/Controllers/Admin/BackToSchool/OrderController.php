<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\BackToSchool\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Traits\DtdcShipmentTrait;

class OrderController extends Controller
{
    use DtdcShipmentTrait;
    public function index(Request $request): View
    {
        $query = Order::with(['school', 'product'])->latest();

        if ($request->has('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        // Apply School Filter
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Apply Grade Filter
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Apply Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Apply Date Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();
        
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        // Fetch distinct grades and categories from orders for filter
        $grades = Order::whereNotNull('grade')->distinct()->pluck('grade')->sort();
        $categories = Order::whereNotNull('category')->distinct()->pluck('category')->sort();
        $filters = $request->all();

        return view('admin.back_to_school.orders.index', compact('orders', 'schools', 'grades', 'categories', 'filters'));
    }

    public function show($id): View
    {
        $order = Order::with('school')->findOrFail($id);
        return view('admin.back_to_school.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        if ($request->input('order_status') === 'cancelled') {
            return $this->cancelShipment($request, $order);
        }

        $request->validate([
            'order_status' => 'required|string|in:order_placed,processing,packed,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
        ]);

        $previousOrderStatus = $order->order_status;
        $order->update([
            'order_status' => $request->order_status,
            'tracking_number' => $request->tracking_number,
        ]);

        // Send Order Status Update Email if order status changed (including delivered status)
        if ($previousOrderStatus !== $order->order_status && !empty($order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderStatusUpdateMail($order, $previousOrderStatus));
                \Illuminate\Support\Facades\Log::info("Order status update email sent to {$order->customer_email} for order {$order->order_number} - Status: {$order->order_status}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send order status update email for order {$order->order_number}: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Order status updated.');
    }
    public function invoiceView($id): View
    {
        $order = Order::with('school')->findOrFail($id);
        return view('admin.back_to_school.orders.invoice', compact('order'));
    }

    public function invoiceDownload($id)
    {
        try {
            $order = Order::with('school')->findOrFail($id);
            
            // Bypass container resolution and instantiate directly
            if (class_exists(\Dompdf\Dompdf::class)) {
                 $dompdf = new \Dompdf\Dompdf();
                 $options = new \Dompdf\Options();
                 $options->set('isRemoteEnabled', true);
                 $options->set('defaultFont', 'sans-serif');
                 $dompdf->setOptions($options);
                 
                 // Reuse the generic PDF view as it has no dependencies on layout
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
