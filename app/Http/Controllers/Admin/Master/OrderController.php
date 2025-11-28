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

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only([
            'school_id',
            'grade',
            'category',
            'order_status',
            'payment_status',
            'date_from',
            'date_to',
            'order_number',
        ]);

        $orders = Order::with('school')
            ->when($filters['school_id'] ?? null, fn($query, $school) => $query->where('school_id', $school))
            ->when($filters['grade'] ?? null, fn($query, $grade) => $query->where('grade', $grade))
            ->when($filters['category'] ?? null, fn($query, $category) => $query->where('category', $category))
            ->when($filters['order_status'] ?? null, fn($query, $status) => $query->where('order_status', $status))
            ->when($filters['payment_status'] ?? null, fn($query, $status) => $query->where('payment_status', $status))
            ->when($filters['order_number'] ?? null, fn($query, $number) => $query->where('order_number', 'like', '%' . $number . '%'))
            ->when($filters['date_from'] ?? null, fn($query, $from) => $query->whereDate('order_date', '>=', Carbon::parse($from)))
            ->when($filters['date_to'] ?? null, fn($query, $to) => $query->whereDate('order_date', '<=', Carbon::parse($to)))
            ->orderByDesc('order_date')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $grades = Order::select('grade')->whereNotNull('grade')->distinct()->orderBy('grade')->pluck('grade');
        $categories = Order::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.orders.index', compact('orders', 'schools', 'grades', 'categories', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load('school');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'order_status' => ['required', 'string', 'max:255'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        $original = $order->only(['order_status', 'payment_status', 'tracking_number']);
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
        $order->load('school');
        $lines = $this->invoiceLines($order);
        $pdf = $this->buildSimplePdf($lines);

        return response()->streamDownload(fn () => print($pdf), $order->order_number . '-invoice.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function invoiceLines(Order $order): array
    {
        return [
            'Invoice #' . $order->order_number,
            'Order Date: ' . optional($order->order_date)->format('d M Y'),
            'School: ' . optional($order->school)->name,
            'Student: ' . $order->student_name . ' • Grade ' . $order->grade,
            'Item: ' . $order->item_name . ' (' . $order->size . ') x ' . $order->quantity,
            'Category: ' . $order->category,
            'Customer: ' . $order->customer_name,
            'Ship to: ' . $order->customer_address,
            'Contact: ' . $order->customer_phone . ' / ' . ($order->customer_email ?? 'N/A'),
            'Totals: ₹' . $order->total_amount . ' (Tax ₹' . $order->tax_amount . ', Shipping ₹' . $order->shipping_cost . ')',
            'Payment: ' . ucfirst($order->payment_status),
            'Status: ' . ucfirst($order->order_status),
        ];
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

