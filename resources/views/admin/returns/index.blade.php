@extends('admin.layouts.base')

@section('title', 'Returns & Exchanges | The Skool Store')
@section('page_heading', 'Returns & Exchanges')
@section('page_subheading', 'Review and approve requests; process received items')

@section('content')
    <div class="card" style="max-width:1200px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
            <form method="GET" action="{{ route('master.admin.returns-exchange.index') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <select name="type" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <option value="">All Types</option>
                    <option value="return" {{ ($filters['type'] ?? '') === 'return' ? 'selected' : '' }}>Return</option>
                    <option value="exchange" {{ ($filters['type'] ?? '') === 'exchange' ? 'selected' : '' }}>Exchange</option>
                </select>
                <select name="status" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','rejected','received_restocked','received_discarded','completed'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }} style="text-transform:capitalize;">{{ str_replace('_',' ', $st) }}</option>
                    @endforeach
                </select>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search order# or item" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;min-width:240px;">
                <button type="submit" style="padding:8px 12px;border:none;border-radius:8px;background:#f3f4f6;color:#111827;">Filter</button>
                <a href="{{ route('master.admin.returns-exchange.index') }}" style="padding:8px 12px;border:none;border-radius:8px;background:#fff;color:#111827;border:1px solid #e5e7eb;text-decoration:none;">Reset</a>
            </form>
        </div>

        <div class="table-wrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:12px;">
            <table class="table" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f9fafb;">
                        <th style="padding:10px;text-align:left;">ID</th>
                        <th style="padding:10px;text-align:left;">Type</th>
                        <th style="padding:10px;text-align:left;">Order #</th>
                        <th style="padding:10px;text-align:left;">Item</th>
                        <th style="padding:10px;text-align:left;">Qty</th>
                        <th style="padding:10px;text-align:left;">Status</th>
                        <th style="padding:10px;text-align:left;">Reason</th>
                        <th style="padding:10px;text-align:left;">Admin Notes</th>
                        <th style="padding:10px;text-align:left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td style="padding:10px;">{{ $req->id }}</td>
                            <td style="padding:10px;text-transform:capitalize;">{{ $req->type }}</td>
                            <td style="padding:10px;">
                                <a href="{{ route('master.admin.orders.show', $req->order) }}" style="color:#490d59;text-decoration:none;">{{ $req->order->order_number ?? '—' }}</a>
                            </td>
                            <td style="padding:10px;">{{ $req->order->item_name ?? '—' }}</td>
                            <td style="padding:10px;">{{ $req->order->quantity ?? 1 }}</td>
                            <td style="padding:10px;text-transform:capitalize;">{{ str_replace('_',' ', $req->status) }}</td>
                            <td style="padding:10px;">{{ $req->reason }}</td>
                            <td style="padding:10px;">{{ $req->admin_notes }}</td>
                            <td style="padding:10px;display:flex;gap:8px;flex-wrap:wrap;">
                                <a href="{{ route('master.admin.returns-exchange.show', $req) }}" style="border:1px solid #e5e7eb;border-radius:8px;padding:6px 10px;text-decoration:none;color:#111827;background:#fff;">View</a>

                                @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('master.admin.returns-exchange.approve', $req) }}">
                                        @csrf
                                        <button type="submit" style="border:none;border-radius:8px;padding:6px 10px;background:#e9d7fe;color:#6941c6;">Approve</button>
                                    </form>
                                @endif

                                @if(in_array($req->status, ['approved']))
                                    @if($req->type === 'return')
                                        <form method="POST" action="{{ route('master.admin.returns-exchange.receive', $req) }}" style="display:flex;gap:6px;">
                                            @csrf
                                            <input type="hidden" name="admin_notes" value="Auto-marked from list">
                                            <button type="submit" name="action" value="restock" style="border:none;border-radius:8px;padding:6px 10px;background:#dcfce7;color:#065f46;">Mark Received - Restock</button>
                                            <button type="submit" name="action" value="discard" style="border:none;border-radius:8px;padding:6px 10px;background:#fee2e2;color:#991b1b;">Mark Received - Discard</button>
                                        </form>
                                    @endif

                                    @if($req->type === 'exchange')
                                        <form method="POST" action="{{ route('master.admin.returns-exchange.generate', $req) }}" style="display:flex;gap:6px;">
                                            @csrf
                                            <input type="text" name="exchange_product_name" placeholder="Replacement Product" required style="padding:6px;border:1px solid #e5e7eb;border-radius:8px;">
                                            <input type="text" name="exchange_size" placeholder="Size" style="padding:6px;border:1px solid #e5e7eb;border-radius:8px;">
                                            <button type="submit" style="border:none;border-radius:8px;padding:6px 10px;background:#dbeafe;color:#1d4ed8;">Generate Exchange Order</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="padding:12px;text-align:center;">No return/exchange requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:12px;">{{ $requests->links() }}</div>
    </div>
@endsection