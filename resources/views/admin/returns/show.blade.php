@extends('admin.layouts.base')

@section('title', 'Return/Exchange Request #'.$returnRequest->id.' | The Skool Store')
@section('page_heading', 'Return/Exchange Request #'.$returnRequest->id)
@section('page_subheading', 'Review details and take action')

@section('content')
    <div class="card" style="max-width:1000px;margin:auto;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;">
                <h4 style="margin:0 0 8px;color:#111827;">Request</h4>
                <p><strong>Type:</strong> <span style="text-transform:capitalize;">{{ $returnRequest->type }}</span></p>
                <p><strong>Status:</strong> <span style="text-transform:capitalize;">{{ str_replace('_',' ', $returnRequest->status) }}</span></p>
                <p><strong>Reason:</strong> {{ $returnRequest->reason }}</p>
                <p><strong>Admin Notes:</strong> {{ $returnRequest->admin_notes ?? '—' }}</p>
                @if($returnRequest->type === 'exchange')
                    <p><strong>Exchange Product:</strong> {{ $returnRequest->exchange_product_name ?? '—' }}</p>
                    <p><strong>Exchange Size:</strong> {{ $returnRequest->exchange_size ?? '—' }}</p>
                    <p><strong>New Order ID:</strong> {{ $returnRequest->new_order_id ?? '—' }}</p>
                @endif
            </div>
            <div style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;">
                <h4 style="margin:0 0 8px;color:#111827;">Original Order</h4>
                <p><strong>Order #:</strong> {{ $returnRequest->order->order_number ?? '—' }}</p>
                <p><strong>School:</strong> {{ $returnRequest->order->school->name ?? '—' }}</p>
                <p><strong>Item:</strong> {{ $returnRequest->order->item_name ?? '—' }}</p>
                <p><strong>Size:</strong> {{ $returnRequest->order->size ?? '—' }}</p>
                <p><strong>Qty:</strong> {{ $returnRequest->order->quantity ?? 1 }}</p>
                <p><a href="{{ route('master.admin.orders.show', $returnRequest->order) }}" style="color:#490d59;text-decoration:none;">View order details →</a></p>
            </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($returnRequest->status === 'pending')
                <form method="POST" action="{{ route('master.admin.returns-exchange.approve', $returnRequest) }}" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <input type="text" name="admin_notes" placeholder="Approval notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <button type="submit" style="border:none;border-radius:8px;padding:8px 12px;background:#e9d7fe;color:#6941c6;">Approve</button>
                </form>
            @endif

            @if($returnRequest->status === 'approved' && $returnRequest->type === 'return')
                <form method="POST" action="{{ route('master.admin.returns-exchange.receive', $returnRequest) }}" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <input type="text" name="admin_notes" placeholder="Receiving notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <button type="submit" name="action" value="restock" style="border:none;border-radius:8px;padding:8px 12px;background:#dcfce7;color:#065f46;">Mark Received - Restock</button>
                    <button type="submit" name="action" value="discard" style="border:none;border-radius:8px;padding:8px 12px;background:#fee2e2;color:#991b1b;">Mark Received - Discard</button>
                </form>
            @endif

            @if($returnRequest->status === 'approved' && $returnRequest->type === 'exchange')
                <form method="POST" action="{{ route('master.admin.returns-exchange.generate', $returnRequest) }}" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <input type="text" name="exchange_product_name" value="{{ $returnRequest->exchange_product_name }}" placeholder="Replacement Product" required style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <input type="text" name="exchange_size" value="{{ $returnRequest->exchange_size }}" placeholder="Size" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <input type="text" name="admin_notes" placeholder="Exchange notes" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <button type="submit" style="border:none;border-radius:8px;padding:8px 12px;background:#dbeafe;color:#1d4ed8;">Generate Exchange Order</button>
                </form>
            @endif
        </div>

        @if(session('status'))
            <div style="margin-top:16px;padding:10px;border-radius:8px;background:#ecfdf5;color:#064e3b;">{{ session('status') }}</div>
        @endif
    </div>
@endsection