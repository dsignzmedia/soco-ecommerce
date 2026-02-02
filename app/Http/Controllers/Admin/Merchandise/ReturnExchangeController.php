<?php

namespace App\Http\Controllers\Admin\Merchandise;

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
            ->whereHas('order', function($q) {
                $q->where('product_type', 'merchandised');
            })
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

        // Fetch product images for the list
        $productImages = [];
        $orders = $requests->pluck('order')->filter();
        
        if ($orders->isNotEmpty()) {
            $productNames = $orders->pluck('item_name')->unique();
            $schoolIds = $orders->pluck('school_id')->unique();
            
            $products = ProductMapping::whereIn('product_name', $productNames)
                ->whereIn('school_id', $schoolIds)
                ->select('product_name', 'school_id', 'featured_image')
                ->get();
                
            foreach ($orders as $order) {
                // Find matching product
                $match = $products->first(function($p) use ($order) {
                    return $p->product_name === $order->item_name && $p->school_id === $order->school_id;
                });
                
                if ($match && $match->featured_image) {
                    // Verify file exists before adding to array
                    $imagePath = storage_path('app/public/' . $match->featured_image);
                    if (file_exists($imagePath)) {
                        $productImages[$order->id] = $match->featured_image;
                    }
                }
            }
        }

        return view('admin.merchandise.returns.index', compact('requests', 'filters', 'productImages'));
    }

    public function show(ReturnExchangeRequest $returnRequest): View
    {
        $returnRequest->load('order');
        
        // Security check: ensure this request belongs to Merchandise products
        if ($returnRequest->order && $returnRequest->order->product_type !== 'merchandised') {
            abort(403, 'This return/exchange request does not belong to Merchandise products.');
        }
        
        $product = null;
        $sizes = collect();
        
        if ($returnRequest->order) {
            $product = ProductMapping::where('product_name', $returnRequest->order->item_name)
                ->where('school_id', $returnRequest->order->school_id)
                ->where('product_type', 'merchandised')
                ->first();

            if (!$product) {
                 $product = ProductMapping::where('product_name', $returnRequest->order->item_name)
                     ->where('product_type', 'merchandised')
                     ->first();
            }
                
            if ($product) {
                $sizes = $product->variants()->where('stock', '>', 0)->get();
            }
        }

        return view('admin.merchandise.returns.show', compact('returnRequest', 'product', 'sizes'));
    }

    public function switchType(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        if ($returnRequest->type === 'return') {
            $returnRequest->update(['type' => 'exchange']);
            
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
            // Use requested_quantity for partial returns, not full order quantity
            $restockQty = $returnRequest->requested_quantity ?? $order->quantity;
            
            $product = ProductMapping::where('product_name', $order->item_name)
                ->where('school_id', $order->school_id)
                ->where('product_type', 'merchandised')
                ->first();

            if ($product) {
                if ($order->size && $product->variants()->exists()) {
                    $variant = $product->variants()->where('option', $order->size)->first();
                    if ($variant) {
                        $before = $variant->stock;
                        $after = $before + $restockQty;
                        $variant->update(['stock' => $after]);
                        $product->updateTotalStock();
                        
                        InventoryAdjustment::create([
                            'product_mapping_id' => $product->id,
                            'quantity_change' => $restockQty,
                            'reason' => 'return_restock',
                            'comment' => "Restock {$restockQty} unit(s) from return for order {$order->order_number} (Variant: {$order->size})",
                            'stock_before' => $before,
                            'stock_after' => $after,
                        ]);
                    } else {
                        $before = $product->inventory_stock;
                        $after = $before + $restockQty;
                        $product->update(['inventory_stock' => $after]);

                        InventoryAdjustment::create([
                            'product_mapping_id' => $product->id,
                            'quantity_change' => $restockQty,
                            'reason' => 'return_restock',
                            'comment' => "Restock {$restockQty} unit(s) from return for order {$order->order_number}",
                            'stock_before' => $before,
                            'stock_after' => $after,
                        ]);
                    }
                } else {
                    $before = $product->inventory_stock;
                    $after = $before + $restockQty;
                    $product->update(['inventory_stock' => $after]);

                    InventoryAdjustment::create([
                        'product_mapping_id' => $product->id,
                        'quantity_change' => $restockQty,
                        'reason' => 'return_restock',
                        'comment' => "Restock {$restockQty} unit(s) from return for order {$order->order_number}",
                        'stock_before' => $before,
                        'stock_after' => $after,
                    ]);
                }
                
                // Update returned_quantity
                $returnRequest->update(['returned_quantity' => $restockQty]);
            }
        } else if ($data['action'] === 'discard' && $order) {
            // Update returned_quantity even for discarded items
            $discardQty = $returnRequest->requested_quantity ?? $order->quantity;
            $returnRequest->update(['returned_quantity' => $discardQty]);
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
            ->where('product_type', 'merchandised')
            ->first();

        // Use requested_quantity for partial exchanges
        $exchangeQty = $returnRequest->requested_quantity ?? $order->quantity;
        
        // Calculate proportional price based on requested quantity
        $unitPrice = $order->quantity > 0 ? ($order->total_amount / $order->quantity) : $order->total_amount;
        $unitTax = $order->quantity > 0 ? ($order->tax_amount / $order->quantity) : $order->tax_amount;
        
        $exchangeAmount = $unitPrice * $exchangeQty;
        $exchangeTax = $unitTax * $exchangeQty;

        $newOrder = Order::create([
            'order_number' => $exchangeNumber,
            'user_id' => $order->user_id, // IMPORTANT: Copy user_id from original order so parents can track it
            'school_id' => $order->school_id,
            'order_date' => now(),
            'student_name' => $order->student_name,
            'grade' => $order->grade,
            'category' => $order->category,
            'product_type' => 'merchandised',
            'item_name' => $data['exchange_product_name'],
            'size' => $data['exchange_size'],
            'quantity' => $exchangeQty,
            'customer_name' => $order->customer_name,
            'customer_address' => $order->customer_address,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'total_amount' => $exchangeAmount,
            'tax_amount' => $exchangeTax,
            'shipping_cost' => 0,
            'payment_status' => 'paid',
            'order_status' => 'pending', // Start with 'pending' (Order Placed) so tracking shows from beginning
            'return_exchange_status' => 'exchange_created',
            'notes' => "Exchange for {$exchangeQty} unit(s) from order {$order->order_number}",
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
                $after = $before - $exchangeQty;
                $variant->update(['stock' => $after]);
                $product->updateTotalStock();
            } else {
                $before = $product->inventory_stock;
                $after = $before - $exchangeQty;
                $product->update(['inventory_stock' => $after]);
            }

            InventoryAdjustment::create([
                'product_mapping_id' => $product->id,
                'quantity_change' => -$exchangeQty,
                'reason' => 'exchange_replace',
                'comment' => "Replacement sent for {$exchangeQty} unit(s) from exchange {$exchangeNumber}" . ($variant ? " (Variant: {$variant->option})" : ''),
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

        return redirect()->route('admin.merchandise.returns-exchange.show', $returnRequest)->with('status', 'Exchange order generated');
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

        // Calculate refund amount proportionally based on requested_quantity
        $unitPrice = $order->quantity > 0 ? ($order->total_amount / $order->quantity) : $order->total_amount;
        $requestedQty = $returnRequest->requested_quantity ?? $order->quantity;
        
        $refundAmount = $unitPrice * $requestedQty;
        
        // If payment amount doesn't match order amount, calculate proportionally
        if ($originalPayment->amount_paid != $order->total_amount && $order->total_amount > 0) {
            $paymentRatio = $originalPayment->amount_paid / $order->total_amount;
            $refundAmount = $refundAmount * $paymentRatio;
        }
        
        $refundAmount = round($refundAmount, 2);

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

