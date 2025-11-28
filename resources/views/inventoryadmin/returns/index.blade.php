@extends('inventoryadmin.layouts.base')

@section('title', 'Returns & Exchanges | The Skool Store')
@section('page_heading', 'Returns & Exchanges')
@section('page_subheading', 'View-only list of requests and statuses')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
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