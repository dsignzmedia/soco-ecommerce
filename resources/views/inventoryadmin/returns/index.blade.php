@extends('inventoryadmin.layouts.base')

@section('title', 'Returns & Exchanges | The Skool Store')
@section('page_heading', 'Returns & Exchanges')
@section('page_subheading', 'View-only list of requests and statuses')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
            <form method="GET" action="{{ route('inventory.admin.returns-exchange.index') }}" style="display:flex;gap:8px;align-items:center;">
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
                <button type="button" style="padding:8px 12px;border:none;border-radius:8px;background:#f3f4f6;color:#111827;" onclick="window.location.href='{{ route('inventory.admin.returns-exchange.index') }}'">Reset</button>
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td style="padding:10px;">{{ $req->id }}</td>
                            <td style="padding:10px;text-transform:capitalize;">{{ $req->type }}</td>
                            <td style="padding:10px;">{{ $req->order->order_number ?? '—' }}</td>
                            <td style="padding:10px;">{{ $req->order->item_name ?? '—' }}</td>
                            <td style="padding:10px;">{{ $req->order->quantity ?? 1 }}</td>
                            <td style="padding:10px;text-transform:capitalize;">{{ str_replace('_',' ', $req->status) }}</td>
                            <td style="padding:10px;">{{ $req->reason }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="padding:12px;text-align:center;">No requests available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px;">{{ $requests->links() }}</div>
    </div>
@endsection