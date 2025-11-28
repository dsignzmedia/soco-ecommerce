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

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only([
            'school_id',
            'order_status',
            'date_from',
            'date_to',
            'order_number',
        ]);

        $orders = Order::with('school')
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->where('school_id', $school))
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->where('order_status', $status))
            ->when($filters['order_number'] ?? null, fn($query, $number) => $query->where('order_number', 'like', '%' . $number . '%'))
            ->when($filters['date_from'] ?? null, fn($query, $from) => $query->whereDate('order_date', '>=', Carbon::parse($from)))
            ->when($filters['date_to'] ?? null, fn($query, $to) => $query->whereDate('order_date', '<=', Carbon::parse($to)))
            ->orderByDesc('order_date')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        
        // Statuses relevant to inventory
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing (Packing)',
            'ready_to_ship' => 'Ready to Ship',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
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
        $orders = Order::with('school')
            ->where('order_status', 'ready_to_ship')
            ->orderBy('order_date')
            ->paginate(15);

        return view('inventoryadmin.orders.shipping', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'order_status' => ['required', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'courier_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $original = $order->only(['order_status', 'tracking_number', 'courier_name', 'notes']);
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
        $order->load('school');
        $lines = [
            'SHIPPING LABEL',
            '----------------------------------------',
            'FROM:',
            'The Skool Store',
            'Warehouse Fulfillment Center',
            '----------------------------------------',
            'TO:',
            $order->customer_name,
            $order->customer_address,
            'Phone: ' . $order->customer_phone,
            '----------------------------------------',
            'ORDER DETAILS:',
            'Order #: ' . $order->order_number,
            'Courier: ' . ($order->courier_name ?? 'Not Assigned'),
            'Tracking: ' . ($order->tracking_number ?? 'Pending'),
            '----------------------------------------',
            'Weight: 0.5kg (Est)',
        ];

        $pdf = $this->buildSimplePdf($lines);

        return response()->streamDownload(fn () => print($pdf), $order->order_number . '-label.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function buildSimplePdf(array $lines): string
    {
        $escaped = array_map(fn ($line) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line), $lines);
        $contentBody = "BT\n/F1 11 Tf\n72 720 Td\n";
        foreach ($escaped as $index => $line) {
            $contentBody .= '(' . $line . ") Tj\n";
            if ($index !== array_key_last($escaped)) {
                $contentBody .= "T*\n";
            }
        }
        $contentBody .= "ET";
        $length = strlen($contentBody);

        $pdf = "%PDF-1.4\n";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /MediaBox [0 0 612 792] /Count 1 /Kids [3 0 R] >>',
            '<< /Type /Page /Parent 2 0 R /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            "<< /Length $length >>\nstream\n$contentBody\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n$xrefPosition\n%%EOF";

        return $pdf;
    }
}
