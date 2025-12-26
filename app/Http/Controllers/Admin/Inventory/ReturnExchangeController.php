<?php

namespace App\Http\Controllers\Admin\Inventory;

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

        return view('inventoryadmin.returns.index', compact('requests', 'filters'));
    }

    public function show(ReturnExchangeRequest $returnRequest): View
    {
        $returnRequest->load('order');
        
        // Fetch product and variants for size dropdown
        $product = null;
        $sizes = collect();
        
        if ($returnRequest->order) {
            $product = ProductMapping::where('product_name', $returnRequest->order->item_name)
                ->where('school_id', $returnRequest->order->school_id)
                ->first();

            if (!$product) {
                 $product = ProductMapping::where('product_name', $returnRequest->order->item_name)->first();
            }
                
            if ($product) {
                $sizes = $product->variants()->where('stock', '>', 0)->get();
            }
        }

        return view('inventoryadmin.returns.show', compact('returnRequest', 'product', 'sizes'));
    }

    public function switchType(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        if ($returnRequest->type === 'return') {
            $returnRequest->update([
                'type' => 'exchange',
            ]);
            
            AuditLogger::record('return_exchange_switch_type', $returnRequest, [
                'order_id' => $returnRequest->order_id,
                'from' => 'return',
                'to' => 'exchange',
            ], 'Switched request type from Return to Exchange');

            return back()->with('status', 'Request type switched to Exchange');
        }

        return back()->with('error', 'Request is already an exchange or cannot be switched.');
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

        // Send Return/Exchange Request Approved Email
        if ($returnRequest->order && !empty($returnRequest->order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($returnRequest->order->customer_email)->send(new \App\Mail\ReturnExchangeRequestMail($returnRequest, 'approved'));
                \Illuminate\Support\Facades\Log::info("Return/Exchange approval email sent to {$returnRequest->order->customer_email} for request #{$returnRequest->id}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send return/exchange approval email: " . $e->getMessage());
            }
        }

        return back()->with('status', 'Request approved');
    }

    public function deny(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes')
        ]);

        AuditLogger::record('return_exchange_deny', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'type' => $returnRequest->type,
            'status' => 'rejected',
        ], 'Denied return/exchange request');

        // Send Return/Exchange Request Rejected Email
        if ($returnRequest->order && !empty($returnRequest->order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($returnRequest->order->customer_email)->send(new \App\Mail\ReturnExchangeRequestMail($returnRequest, 'rejected'));
                \Illuminate\Support\Facades\Log::info("Return/Exchange rejection email sent to {$returnRequest->order->customer_email} for request #{$returnRequest->id}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send return/exchange rejection email: " . $e->getMessage());
            }
        }

        return back()->with('status', 'Request denied');
    }

    public function receive(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:restock,discard'],
            'admin_notes' => ['nullable', 'string']
        ]);

        $order = $returnRequest->order;
        $status = $data['action'] === 'restock' ? 'received_restocked' : 'received_discarded';
        $returnRequest->update([
            'status' => $status,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($data['action'] === 'restock' && $order) {
            $product = ProductMapping::where('product_name', $order->item_name)
                ->where('school_id', $order->school_id)
                ->first();

            if ($product) {
                // Check if product has variants and the order has a size
                if ($order->size && $product->variants()->exists()) {
                    $variant = $product->variants()->where('option', $order->size)->first();
                    if ($variant) {
                        $before = $variant->stock;
                        $after = $before + (int)($order->quantity ?? 0);
                        $variant->update(['stock' => $after]);
                        $product->updateTotalStock();
                        
                        InventoryAdjustment::create([
                            'product_mapping_id' => $product->id,
                            'quantity_change' => (int)($order->quantity ?? 0),
                            'reason' => 'return_restock',
                            'comment' => 'Restock from return for order '.$order->order_number . ' (Variant: '.$order->size.')',
                            'stock_before' => $before,
                            'stock_after' => $after,
                        ]);
                    } else {
                        // Variant not found, update main stock
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
                } else {
                    // No variant, update main stock
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
        }

        AuditLogger::record('return_exchange_receive', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'type' => $returnRequest->type,
            'action' => $data['action'],
            'status' => $status,
        ], 'Marked received for return/exchange request');

        // Send Return Item Received Email
        if ($returnRequest->order && !empty($returnRequest->order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($returnRequest->order->customer_email)->send(new \App\Mail\ReturnExchangeRequestMail($returnRequest, 'received'));
                \Illuminate\Support\Facades\Log::info("Return received email sent to {$returnRequest->order->customer_email} for request #{$returnRequest->id}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send return received email: " . $e->getMessage());
            }
        }

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
        $exchangeNumber = $this->generateExchangeNumber($order);

        $product = ProductMapping::where('product_name', $data['exchange_product_name'])
            ->where('school_id', $order->school_id)
            ->first();

        $price = $order->total_amount; 

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
            'total_amount' => $price,
            'tax_amount' => $order->tax_amount,
            'shipping_cost' => 0,
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'return_exchange_status' => 'exchange_created',
            'notes' => 'Exchange for order '.$order->order_number,
        ]);

        $returnRequest->update([
            'status' => 'completed',
            'exchange_product_name' => $data['exchange_product_name'],
            'exchange_size' => $data['exchange_size'],
            'new_order_id' => $newOrder->id,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($product) {
            $variant = $product->variants()->where('option', $data['exchange_size'])->first();
            
            if ($variant) {
                $before = $variant->stock;
                $after = $before - (int)($order->quantity ?? 1);
                $variant->update(['stock' => $after]);
                $product->updateTotalStock();
            } else {
                $before = $product->inventory_stock;
                $after = $before - (int)($order->quantity ?? 1);
                $product->update(['inventory_stock' => $after]);
            }

            InventoryAdjustment::create([
                'product_mapping_id' => $product->id,
                'quantity_change' => - (int)($order->quantity ?? 1),
                'reason' => 'exchange_replace',
                'comment' => 'Replacement sent for exchange '.$exchangeNumber . ($variant ? " (Variant: {$variant->option})" : ''),
                'stock_before' => $before,
                'stock_after' => $after,
            ]);
        }

        AuditLogger::record('return_exchange_generate', $returnRequest, [
            'order_id' => $returnRequest->order_id,
            'exchange_number' => $exchangeNumber,
            'new_order_id' => $newOrder->id,
        ], 'Generated exchange order');

        // Send Exchange Order Generated Email
        if (!empty($newOrder->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($newOrder->customer_email)->send(new \App\Mail\ExchangeOrderMail($newOrder, $order));
                \Illuminate\Support\Facades\Log::info("Exchange order email sent to {$newOrder->customer_email} for exchange order {$exchangeNumber}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send exchange order email: " . $e->getMessage());
            }
        }

        return redirect()->route('inventory.admin.returns-exchange.show', $returnRequest)->with('status', 'Exchange order generated');
    }

    protected function generateExchangeNumber(Order $order): string
    {
        return 'EXC-' . $order->order_number;
    }

    public function refund(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        if ($returnRequest->type !== 'return') {
            return back()->with('error', 'Only return requests can be refunded.');
        }
        
        if (!in_array($returnRequest->status, ['approved', 'received_restocked', 'received_discarded'])) {
            return back()->with('error', 'Request must be approved or received to process refund.');
        }

        $order = $returnRequest->order;
        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        $originalPayment = \App\Models\Payment::where('order_id', $order->id)
            ->where('payment_status', 'paid')
            ->orderBy('id', 'desc')
            ->first();

        if (!$originalPayment) {
            \Illuminate\Support\Facades\Log::error("Refund Failed: No payment found for Order ID {$order->id}");
            return back()->with('error', 'Original successful payment not found for this order.');
        }

        \Illuminate\Support\Facades\Log::info("Refund Initiated: Order ID {$order->id}, Payment ID {$originalPayment->payment_id}");

        $refundAmount = $originalPayment->amount_paid;

        $existingRefund = \App\Models\Payment::where('order_id', $order->id)
            ->where('payment_for', 'refund')
            ->exists();
        
        if ($existingRefund) {
            return back()->with('error', 'Refund already processed for this order.');
        }

        try {
            $razorpayService = new \App\Services\RazorpayService();
            $refundData = $razorpayService->refund($originalPayment->payment_id, $refundAmount, [
                'return_request_id' => $returnRequest->id,
                'reason' => 'Return Request #' . $returnRequest->id
            ]);

            \App\Models\Payment::create([
                'order_id' => $order->id,
                'product_type' => $order->product_type,
                'payment_id' => $refundData['id'] ?? ('ref_' . time()),
                'total_amount' => $refundAmount,
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'amount_paid' => $refundAmount,
                'payment_status' => 'refunded',
                'payment_method' => $originalPayment->payment_method,
                'payment_type' => 'online',
                'payment_details' => $refundData,
                'payment_for' => 'refund',
            ]);

            $returnRequest->update([
                'status' => 'completed',
                'admin_notes' => ($returnRequest->admin_notes ?? '') . "\nRefund Processed: ₹{$refundAmount}"
            ]);

            $order->update(['payment_status' => 'refunded']);

            AuditLogger::record('payment_refund', $returnRequest, [
                'amount' => $refundAmount,
                'payment_id' => $originalPayment->payment_id,
                'refund_id' => $refundData['id'] ?? null
            ], 'Processed refund via Razorpay');

            // Send Refund Processed Email
            if (!empty($order->customer_email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\RefundProcessedMail($order, $refundAmount, $refundData['id'] ?? null));
                    \Illuminate\Support\Facades\Log::info("Refund processed email sent to {$order->customer_email} for order {$order->order_number}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send refund processed email: " . $e->getMessage());
                }
            }

            return back()->with('status', 'Refund processed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}
