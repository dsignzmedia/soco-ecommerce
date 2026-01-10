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

        $requests = ReturnExchangeRequest::with(['order' => fn($q) => $q->withoutGlobalScopes()])
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, function ($q, $term) {
                $q->whereHas('order', function ($oq) use ($term) {
                    $oq->withoutGlobalScopes()
                        ->where(function($sub) use ($term) {
                            $sub->where('order_number', 'like', '%'.$term.'%')
                                ->orWhere('item_name', 'like', '%'.$term.'%');
                        });
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
                    $productImages[$order->id] = $match->featured_image;
                }
            }
        }

        return view('admin.returns.index', compact('requests', 'filters', 'productImages'));
    }

    public function show(ReturnExchangeRequest $returnRequest): View
    {
        $returnRequest->load(['order' => fn($q) => $q->withoutGlobalScopes()]);
        
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
                // Assuming sizes are stored in ProductVariant model linked to ProductMapping
                // If using variants relation:
                $sizes = $product->variants()->where('stock', '>', 0)->get();
            }
        }

        return view('admin.returns.show', compact('returnRequest', 'product', 'sizes'));
    }

    public function switchType(Request $request, ReturnExchangeRequest $returnRequest): RedirectResponse
    {
        if ($returnRequest->type === 'return') {
            $returnRequest->update([
                'type' => 'exchange',
                // Keep status as is, or ensure it's actionable
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

        $order = $returnRequest->order; // Required for inventory impact
        $status = $data['action'] === 'restock' ? 'received_restocked' : 'received_discarded';
        $returnRequest->update([
            'status' => $status,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($data['action'] === 'restock' && $order) {
            // Use requested_quantity for partial returns, not full order quantity
            $restockQty = $returnRequest->requested_quantity ?? $order->quantity;
            
            // Attempt to map back to a ProductMapping by product name + school
            $product = ProductMapping::where('product_name', $order->item_name)
                ->where('school_id', $order->school_id)
                ->first();

            if ($product) {
                // Handle variant-based products
                if ($product->variants()->exists() && $order->size) {
                    $variant = $product->variants()->where('option', $order->size)->first();
                    if ($variant) {
                        $before = $variant->stock;
                        $after = $before + $restockQty;
                        $variant->update(['stock' => $after]);
                        $product->updateTotalStock(); // Recalculate total
                        
                        InventoryAdjustment::create([
                            'product_mapping_id' => $product->id,
                            'quantity_change' => $restockQty,
                            'reason' => 'return_restock',
                            'comment' => "Restock {$restockQty} unit(s) from return for order {$order->order_number} (Size: {$order->size})",
                            'stock_before' => $before,
                            'stock_after' => $after,
                        ]);
                    }
                } else {
                    // Regular product restocking
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

        // Create a new order to represent the exchange shipment
        // Create a new order to represent the exchange shipment
        $product = ProductMapping::where('product_name', $data['exchange_product_name'])
            ->where('school_id', $order->school_id)
            ->first();

        // Use requested_quantity for partial exchanges
        $exchangeQty = $returnRequest->requested_quantity ?? $order->quantity;
        
        // Calculate proportional price based on requested quantity
        // Unit price = total_amount / order quantity
        $unitPrice = $order->quantity > 0 ? ($order->total_amount / $order->quantity) : $order->total_amount;
        $unitTax = $order->quantity > 0 ? ($order->tax_amount / $order->quantity) : $order->tax_amount;
        
        // Calculate exchange amount (proportional to requested quantity)
        $exchangeAmount = $unitPrice * $exchangeQty;
        $exchangeTax = $unitTax * $exchangeQty;

        $newOrder = Order::create([
            'order_number' => $exchangeNumber,
            'school_id' => $order->school_id,
            'order_date' => now(),
            'student_name' => $order->student_name,
            'grade' => $order->grade,
            'category' => $order->category,
            'item_name' => $data['exchange_product_name'],
            'size' => $data['exchange_size'],
            'quantity' => $exchangeQty, // Use requested quantity, not full order quantity
            'customer_name' => $order->customer_name,
            'customer_address' => $order->customer_address,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'total_amount' => $exchangeAmount,
            'tax_amount' => $exchangeTax,
            'shipping_cost' => 0, // Free shipping for exchange usually? Or copy original? Let's say 0 for now as it's admin generated.
            'payment_status' => 'paid', // Mark as paid since it's an exchange
            'order_status' => 'processing', // Ready to process
            'return_exchange_status' => 'exchange_created',
            'notes' => "Exchange for {$exchangeQty} unit(s) from order {$order->order_number}",
        ]);

        // Link back to request
        $returnRequest->update([
            'status' => 'completed',
            'exchange_product_name' => $data['exchange_product_name'],
            'exchange_size' => $data['exchange_size'],
            'new_order_id' => $newOrder->id,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        // Decrement inventory for the SPECIFIC VARIANT (use exchangeQty, not full order quantity)
        if ($product) {
            $variant = $product->variants()->where('option', $data['exchange_size'])->first();
            
            if ($variant) {
                // Deduct from variant (use exchangeQty)
                $before = $variant->stock;
                $after = $before - $exchangeQty;
                $variant->update(['stock' => $after]);

                // Sync total stock
                $product->updateTotalStock();
            } else {
                // Fallback to main stock if no variant found (shouldn't happen if selected from dropdown)
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

        return redirect()->route('master.admin.returns-exchange.show', $returnRequest)->with('status', 'Exchange order generated');
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
        
        // Ensure request is approved or physically received before refunding? 
        // User asked for "near to approve", so likely after approval.
        // Let's allow it if status is 'approved' OR 'received_restocked'/'received_discarded'.
        if (!in_array($returnRequest->status, ['approved', 'received_restocked', 'received_discarded'])) {
            return back()->with('error', 'Request must be approved or received to process refund.');
        }

        $order = $returnRequest->order;
        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        // Find original payment
        // Relaxed query: Try to find any successful payment for this order if 'payment_for' is not strictly 'order'
        $originalPayment = \App\Models\Payment::where('order_id', $order->id)
            ->where('payment_status', 'paid')
            ->orderBy('id', 'desc') // Get the most recent valid payment
            ->first();

        if (!$originalPayment) {
            \Illuminate\Support\Facades\Log::error("Refund Failed: No payment found for Order ID {$order->id}");
            return back()->with('error', 'Original successful payment not found for this order.');
        }

        \Illuminate\Support\Facades\Log::info("Refund Initiated: Order ID {$order->id}, Payment ID {$originalPayment->payment_id}");

        // Calculate refund amount proportionally based on requested_quantity
        // Unit price = total_amount / order quantity
        $unitPrice = $order->quantity > 0 ? ($order->total_amount / $order->quantity) : $order->total_amount;
        $requestedQty = $returnRequest->requested_quantity ?? $order->quantity;
        
        // Calculate proportional refund amount
        $refundAmount = $unitPrice * $requestedQty;
        
        // If payment amount doesn't match order amount, calculate proportionally
        if ($originalPayment->amount_paid != $order->total_amount && $order->total_amount > 0) {
            $paymentRatio = $originalPayment->amount_paid / $order->total_amount;
            $refundAmount = $refundAmount * $paymentRatio;
        }
        
        // Round to 2 decimal places
        $refundAmount = round($refundAmount, 2);

        // Check if already refunded
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

            // Create Refund Record in Payments Table
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'product_type' => $order->product_type,
                'payment_id' => $refundData['id'] ?? ('ref_' . time()), // Refund ID from Razorpay
                'total_amount' => -1 * $refundAmount, // Negative to show deduction? Or just positive with type refund? User said "payment_for" column handles distiction.
                // Let's keep amounts positive but distinction via payment_for, OR negative for financial purity.
                // Usually refunds are outgoing, so negative flow. But keeping it positive and using type is easier for "Amount: ₹500 (Refund)".
                // Let's stick to positive amount but 'payment_for' = 'refund'.
                'total_amount' => $refundAmount, 
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'amount_paid' => $refundAmount, // The amount refunded
                'payment_status' => 'refunded',
                'payment_method' => $originalPayment->payment_method,
                'payment_type' => 'online', // It's an online refund
                'payment_details' => $refundData,
                'payment_for' => 'refund',
            ]);

            // Update Return Request Status
            $returnRequest->update([
                // 'status' => 'completed', // Or keep current status and just mark refunded?
                // Let's mark as completed if funds returned.
                'status' => 'completed',
                'admin_notes' => $returnRequest->admin_notes . "\nRefund Processed: ₹{$refundAmount}"
            ]);

            // Update Order Payment Status
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