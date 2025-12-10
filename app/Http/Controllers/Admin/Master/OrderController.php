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
        $pdf = Pdf::loadView('admin.orders.invoice-pdf', compact('order'));
        return $pdf->download($order->order_number . '-invoice.pdf');
    }
}
