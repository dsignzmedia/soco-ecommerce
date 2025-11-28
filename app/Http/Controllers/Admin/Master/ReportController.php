<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['school_id', 'grade', 'category', 'date_from', 'date_to', 'product_name', 'status']);

        $reportTypes = [
            ['label' => 'Orders', 'description' => 'Order counts, status, fulfilment SLAs'],
            ['label' => 'Revenue', 'description' => 'Gross vs net revenue, tax, shipping'],
            ['label' => 'Product Performance', 'description' => 'Best sellers, velocity, returns'],
            ['label' => 'Stock', 'description' => 'In stock/out of stock/aging snapshot'],
            ['label' => 'Shipping Cost', 'description' => 'Average cost per order, per zone'],
            ['label' => 'Tax', 'description' => 'Tax collected per period/tax profile'],
            ['label' => 'School-wise', 'description' => 'Orders and revenue by school'],
            ['label' => 'Grade wise', 'description' => 'Demand by grade segments'],
            ['label' => 'Category-wise', 'description' => 'Revenue & units by category'],
            ['label' => 'Return/Exchange', 'description' => 'Return rates, reasons, processing time'],
        ];

        $orders = Order::with('school')->latest()->take(5)->get();
        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.reports.index', compact('reportTypes', 'filters', 'orders', 'schools', 'categories'));
    }

    public function export(Request $request, string $type)
    {
        $type = strtolower($type);
        abort_unless(in_array($type, ['csv', 'excel', 'pdf'], true), 404);

        $filters = $request->only(['school_id', 'grade', 'category', 'date_from', 'date_to', 'product_name', 'status']);

        $orders = Order::query()
            ->when($filters['school_id'] ?? null, fn ($q, $school) => $q->where('school_id', $school))
            ->when($filters['grade'] ?? null, fn ($q, $grade) => $q->where('grade', $grade))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('order_status', $status))
            ->when($filters['product_name'] ?? null, fn ($q, $product) => $q->where('item_name', 'like', '%' . $product . '%'))
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('order_date', '<=', $to))
            ->get();

        return match ($type) {
            'csv' => $this->downloadDelimited($orders, ',', 'reports.csv', 'text/csv'),
            'excel' => $this->downloadDelimited($orders, "\t", 'reports.xls', 'application/vnd.ms-excel'),
            'pdf' => $this->downloadPdf($orders),
        };
    }

    protected function downloadDelimited(Collection $orders, string $delimiter, string $filename, string $contentType)
    {
        return response()->streamDownload(function () use ($orders, $delimiter) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order ID', 'Date', 'School', 'Student', 'Grade', 'Category', 'Item', 'Qty', 'Amount', 'Tax', 'Shipping', 'Status'], $delimiter);
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    optional($order->order_date)->format('Y-m-d'),
                    optional($order->school)->name,
                    $order->student_name,
                    $order->grade,
                    $order->category,
                    $order->item_name,
                    $order->quantity,
                    $order->total_amount,
                    $order->tax_amount,
                    $order->shipping_cost,
                    $order->order_status,
                ], $delimiter);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => $contentType]);
    }

    protected function downloadPdf(Collection $orders)
    {
        $lines = [
            'Reports export',
            str_repeat('-', 40),
        ];

        foreach ($orders as $order) {
            $lines[] = $order->order_number . ' • ' . optional($order->order_date)->format('d M Y');
            $lines[] = 'School: ' . optional($order->school)->name . ' • Grade ' . ($order->grade ?? 'n/a');
            $lines[] = 'Item: ' . $order->item_name . ' x' . $order->quantity . ' • ₹' . number_format($order->total_amount, 2);
            $lines[] = 'Status: ' . ucfirst($order->order_status);
            $lines[] = '';
        }

        $pdf = $this->buildSimplePdf($lines);

        return response()->streamDownload(fn () => print($pdf), 'reports.pdf', [
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

