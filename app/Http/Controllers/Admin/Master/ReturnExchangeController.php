<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ReturnExchangeRequest;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\InventoryAdjustment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnExchangeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['type', 'status', 'q']);

        $requests = ReturnExchangeRequest::with('order')
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, function ($q, $term) {
                $q->whereHas('order', function ($oq) use ($term) {
                    $oq->where('order_number', 'like', '%'.$term.'%')
                        ->orWhere('item_name', 'like', '%'.$term.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.returns.index', compact('requests', 'filters'));
    }

    public function show(ReturnExchangeRequest $returnRequest): View
    {
        $returnRequest->load('order');
        return view('admin.returns.show', compact('returnRequest'));
    }

    public function approve(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->input('admin_notes')
        ]);

        AuditLogger::record('return_exchange_approve', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'type' => $returnRequest->type,
            'status' => 'approved',
        ], 'Approved return/exchange request');

        return back()->with('status', 'Request approved');
    }

    public function receive(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:restock,discard'],
            'admin_notes' => ['nullable', 'string']
        ]);

        $order = $returnRequest->order; // Required for inventory impact
        $status = $data['action'] === 'restock' ? 'received_restocked' : 'received_discarded';
        $returnRequest->update([
            'status' => $status,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($data['action'] === 'restock' && $order) {
            // Attempt to map back to a ProductMapping by product name + school
            $product = ProductMapping::where('product_name', $order->item_name)
                ->where('school_id', $order->school_id)
                ->first();

            if ($product) {
                $before = $product->inventory_stock;
                $after = $before + (int)($order->quantity ?? 0);
                $product->update(['inventory_stock' => $after]);

                InventoryAdjustment::create([
                    'product_mapping_id' => $product->id,
                    'quantity_change' => (int)($order->quantity ?? 0),
                    'reason' => 'return_restock',
                    'comment' => 'Restock from return for order '.$order->order_number,
                    'stock_before' => $before,
                    'stock_after' => $after,
                ]);
            }
        }

        AuditLogger::record('return_exchange_receive', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'type' => $returnRequest->type,
            'action' => $data['action'],
            'status' => $status,
        ], 'Marked received for return/exchange request');

        return back()->with('status', 'Request updated');
    }

    public function generateExchange(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        $data = $request->validate([
            'exchange_product_name' => ['required', 'string', 'max:255'],
            'exchange_size' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string']
        ]);

        $order = $returnRequest->order;
        $exchangeNumber = $this->generateExchangeNumber();

        // Create a new order to represent the exchange shipment
        $newOrder = Order::create([
            'order_number' => $exchangeNumber,
            'school_id' => $order->school_id,
            'order_date' => now(),
            'student_name' => $order->student_name,
            'grade' => $order->grade,
            'category' => $order->category,
            'item_name' => $data['exchange_product_name'],
            'size' => $data['exchange_size'],
            'quantity' => $order->quantity ?? 1,
            'customer_name' => $order->customer_name,
            'customer_address' => $order->customer_address,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'total_amount' => $order->total_amount,
            'tax_amount' => $order->tax_amount,
            'shipping_cost' => $order->shipping_cost,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'return_exchange_status' => 'exchange_created',
            'notes' => 'Exchange for order '.$order->order_number,
        ]);

        // Link back to request
        $returnRequest->update([
            'status' => 'completed',
            'exchange_product_name' => $data['exchange_product_name'],
            'exchange_size' => $data['exchange_size'],
            'new_order_id' => $newOrder->id,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        // Decrement inventory for the exchange shipment if possible
        $product = ProductMapping::where('product_name', $data['exchange_product_name'])
            ->where('school_id', $order->school_id)
            ->first();
        if ($product) {
            $before = $product->inventory_stock;
            $after = $before - (int)($order->quantity ?? 1);
            $product->update(['inventory_stock' => $after]);

            InventoryAdjustment::create([
                'product_mapping_id' => $product->id,
                'quantity_change' => - (int)($order->quantity ?? 1),
                'reason' => 'exchange_replace',
                'comment' => 'Replacement sent for exchange '.$exchangeNumber,
                'stock_before' => $before,
                'stock_after' => $after,
            ]);
        }

        AuditLogger::record('return_exchange_generate', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'exchange_number' => $exchangeNumber,
            'new_order_id' => $newOrder->id,
        ], 'Generated exchange order');

        return redirect()->route('master.admin.returns-exchange.show', $returnRequest)->with('status', 'Exchange order generated');
    }

    protected function generateExchangeNumber(): string
    {
        return 'EXCH-'.now()->format('Ymd').'-'.str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}