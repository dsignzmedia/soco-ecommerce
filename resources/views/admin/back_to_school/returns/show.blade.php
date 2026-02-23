@extends('admin.layouts.back_to_school')

@section('title', 'Exchange Request #'.$returnRequest->id.' | The Skool Store')
@section('page_heading', 'Exchange Request #' . $returnRequest->id)

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">
        <a href="{{ route('admin.back_to_school.returns-exchange.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to returns
        </a>
    </div>

    <div class="card" style="max-width:1000px;margin:auto;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;">
                <h4 style="margin:0 0 8px;color:#111827;">Request</h4>
                <p><strong>Type:</strong> <span style="text-transform:capitalize;">{{ $returnRequest->type }}</span></p>
                <p><strong>Status:</strong> <span style="text-transform:capitalize;">{{ str_replace('_',' ', $returnRequest->status) }}</span></p>
                <p><strong>Quantity:</strong> 
                    <span style="color: #490D59; font-weight: 600;">{{ $returnRequest->requested_quantity ?? $returnRequest->order->quantity }}</span>
                    @if($returnRequest->order && ($returnRequest->requested_quantity ?? $returnRequest->order->quantity) < $returnRequest->order->quantity)
                        <span class="badge bg-warning text-dark ms-2">Partial Exchange</span>
                    @endif
                    of {{ $returnRequest->order->quantity ?? 'N/A' }} ordered
                </p>
                @if($returnRequest->returned_quantity)
                    <p><strong>Returned Quantity:</strong> <span style="color: #28a745; font-weight: 600;">{{ $returnRequest->returned_quantity }}</span></p>
                @endif
                <p><strong>Reason:</strong> {{ $returnRequest->reason }}</p>
                <p><strong>Admin Notes:</strong> {{ $returnRequest->admin_notes ?? '—' }}</p>
                @if($returnRequest->type === 'exchange')
                    <p><strong>Exchange Product:</strong> {{ $returnRequest->exchange_product_name ?? '—' }}</p>
                    <p><strong>Exchange Size:</strong> {{ $returnRequest->exchange_size ?? '—' }}</p>
                    <p><strong>New Order ID:</strong> {{ $returnRequest->new_order_id ?? '—' }}</p>
                @endif
                
                @php
                    $photoPaths = $returnRequest->photo_paths ?? [];
                @endphp
                @if(!empty($photoPaths))
                    <div style="margin-top:16px;">
                        <p><strong>Evidence Photos:</strong></p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($photoPaths as $photoPath)
                                <a href="{{ asset('storage/'.$photoPath) }}" target="_blank" style="display:inline-block;">
                                    <img src="{{ asset('storage/'.$photoPath) }}" alt="Evidence" style="max-width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;">
                <h4 style="margin:0 0 8px;color:#111827;">Original Order</h4>
                <p><strong>Order #:</strong> {{ $returnRequest->order->order_number ?? '—' }}</p>
                <p><strong>School:</strong> {{ $returnRequest->order->school->name ?? '—' }}</p>
                <p><strong>Item:</strong> {{ $returnRequest->order->item_name ?? '—' }}</p>
                <p><strong>Size:</strong> {{ $returnRequest->order->size ?? '—' }}</p>
                <p><strong>Ordered Qty:</strong> {{ $returnRequest->order->quantity ?? 1 }}</p>
                @php
                    $totalReturned = \App\Models\Admin\Master\ReturnExchangeRequest::where('order_id', $returnRequest->order_id)
                        ->whereIn('status', ['pending', 'approved', 'received_restocked', 'received_discarded', 'completed'])
                        ->sum('requested_quantity');
                    $remainingQty = ($returnRequest->order->quantity ?? 1) - $totalReturned;
                @endphp
                <p><strong>Returned Qty:</strong> 
                    <span style="color: #dc3545;">{{ $totalReturned }}</span>
                </p>
                <p><strong>Remaining Qty:</strong> 
                    <span style="color: #28a745; font-weight: 600;">{{ $remainingQty }}</span>
                </p>
                <p><a href="{{ route('admin.back_to_school.orders.show', $returnRequest->order) }}" style="color:#490d59;text-decoration:none;">View order details →</a></p>
            </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($returnRequest->status === 'pending')
                <div style="display:flex; gap:8px;">
                    <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.approve', $returnRequest) }}" style="display:flex;gap:8px;align-items:center;">
                        @csrf
                        <input type="text" name="admin_notes" placeholder="Approval notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                        <button type="submit" style="border:none;border-radius:8px;padding:8px 12px;background:#e9d7fe;color:#6941c6;">Approve</button>
                    </form>

                    @if($returnRequest->type === 'return')
                        <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.switch-type', $returnRequest) }}" onsubmit="return confirm('Are you sure you want to switch this request to an Exchange?');">
                            @csrf
                            <button type="submit" style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;background:#f3f4f6;color:#374151;">Switch to Exchange</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.deny', $returnRequest) }}" onsubmit="return confirm('Are you sure you want to deny this request?');">
                        @csrf
                        <button type="submit" style="border:none;border-radius:8px;padding:8px 12px;background:#ffe4e6;color:#be123c;">Deny</button>
                    </form>
                </div>
            @endif

            @if(in_array($returnRequest->status, ['approved', 'received_restocked', 'received_discarded']) && $returnRequest->type === 'return')
                @if($returnRequest->status === 'approved')
                    <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.receive', $returnRequest) }}" style="display:flex;gap:8px;align-items:center;">
                        @csrf
                        <input type="text" name="admin_notes" placeholder="Receiving notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                        <button type="submit" name="action" value="restock" style="border:none;border-radius:8px;padding:8px 12px;background:#dcfce7;color:#065f46;">Mark Received - Restock</button>
                        <button type="submit" name="action" value="discard" style="border:none;border-radius:8px;padding:8px 12px;background:#fee2e2;color:#991b1b;">Mark Received - Discard</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.refund', $returnRequest) }}" onsubmit="return confirm('Are you sure you want to process a refund for this request? This will initiate a Razorpay refund.');">
                    @csrf
                    <button type="submit" class="btn-action-sm" style="border:none;border-radius:8px;padding:8px 12px;background:#e0f2fe;color:#0284c7;" title="Initiate Refund">
                        <i class="fas fa-undo-alt" style="margin-right:4px;"></i> Refund
                    </button>
                </form>
            @endif

            @if($returnRequest->status === 'approved' && $returnRequest->type === 'exchange')
                <form method="POST" action="{{ route('admin.back_to_school.returns-exchange.generate', $returnRequest) }}" style="display:grid;gap:8px;max-width:400px;">
                    @csrf
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:0.85rem;color:#6b7280;">Exchange Product</label>
                        <input type="text" name="exchange_product_name" value="{{ $returnRequest->order->item_name }}" readonly required style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;background-color:#f9fafb;">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:0.85rem;color:#6b7280;">New Size</label>
                        @if(isset($sizes) && $sizes->count() > 0)
                            <select name="exchange_size" required style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                                <option value="">Select available size...</option>
                                @foreach($sizes as $variant)
                                    <option value="{{ $variant->option }}" {{ $returnRequest->exchange_size === $variant->option ? 'selected' : '' }}>{{ $variant->option }} ({{ $variant->stock }} in stock)</option>
                                @endforeach
                            </select>
                            @if($returnRequest->exchange_size)
                                <small style="color:#490d59;font-weight:500;margin-top:4px;display:block;"><i class="fas fa-user me-1"></i>Customer requested: <strong>{{ $returnRequest->exchange_size }}</strong></small>
                            @endif
                        @else
                            <input type="text" name="exchange_size" value="{{ $returnRequest->exchange_size ?? '' }}" placeholder="Enter size manually" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                        @endif
                    </div>

                    <input type="text" name="admin_notes" placeholder="Exchange notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <button type="submit" style="border:none;border-radius:8px;padding:10px;background:#dbeafe;color:#1d4ed8;font-weight:500;">Generate Exchange Order</button>
                </form>
            @endif
        </div>

        @if(session('status'))
            <div style="margin-top:16px;padding:10px;border-radius:8px;background:#ecfdf5;color:#064e3b;">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div style="margin-top:16px;padding:10px;border-radius:8px;background:#fee2e2;color:#991b1b;">{{ session('error') }}</div>
        @endif
    </div>
@endsection

