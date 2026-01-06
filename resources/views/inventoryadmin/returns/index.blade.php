@extends('inventoryadmin.layouts.base')

@section('title', 'Returns & Exchanges | The Skool Store')
@section('page_heading', 'Returns & Exchanges')
@section('page_subheading', 'Review and approve requests; process received items')

@section('content')
    <style>
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .filter-form-grid {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-input-rounded {
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px;
            font-family: inherit;
            min-width: 150px;
            flex: 1;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }
        .btn-filter, .btn-reset {
            height: 46px;
            padding: 0 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        .btn-filter {
            background-color: #490d59;
            color: #ffffff;
            border: none;
        }
        .btn-filter:hover {
            background-color: #3b0a48;
        }
        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            text-decoration: none;
        }
        .btn-reset:hover {
            border-color: #d0d5dd;
            color: #0f172a;
            background: #f8fafc;
        }

        .action-group {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-action-sm {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            transition: opacity 0.2s;
        }
        .btn-action-sm:hover { opacity: 0.9; }
        
        .btn-view { background: #fff; border: 1px solid #d0d5dd; color: #344054; }
        .btn-approve { background: #dcfce7; color: #027a48; }
        .btn-deny { background: #fee4e2; color: #b42318; }
        .btn-blue { background: #e0f2fe; color: #026aa7; }
        
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-bottom: none;
            overflow-x: auto;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 13px; vertical-align: middle; }
        th { background: #f9fafb; font-weight: 600; color: #475467; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }

        th:first-child, td:first-child {
            padding-left: 32px;
        }

        /* Pagination Containers and Buttons */
        .pagination-container {
            padding: 12px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        .pagination-container nav > div:first-child { display: none !important; } /* Hide mobile text */
        .pagination-container nav > div:last-child {
            display: flex !important;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }
        .pagination-container p { font-size: 13px; color: #6b7280; margin: 0; }
        .pagination-container nav span[class*="shadow-sm"],
        .pagination-container nav div[class*="shadow-sm"] {
            box-shadow: none !important;
            display: inline-flex;
            gap: 4px;
        }
        .pagination-container nav a, 
        .pagination-container nav span[aria-disabled], 
        .pagination-container nav span[aria-current="page"] > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            width: 36px !important;
            height: 36px !important;
            margin: 0 !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }
        .pagination-container nav span[aria-current="page"] > span {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color: white !important;
        }
        .pagination-container nav span[aria-disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f9fafb;
        }
        .pagination-container nav a:hover {
            background-color: #f3e8f5;
            border-color: #490d59 !important;
            color: #490d59;
        }
        .pagination-container nav svg { width: 16px; height: 16px; }
    </style>

    <div class="filters-card">
        <form method="GET" action="{{ route('inventory.admin.returns-exchange.index') }}" class="filter-form-grid">
            <div style="flex:1; min-width: 150px;">
                <select name="type" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Types</option>
                    <option value="return" {{ ($filters['type'] ?? '') === 'return' ? 'selected' : '' }}>Return</option>
                    <option value="exchange" {{ ($filters['type'] ?? '') === 'exchange' ? 'selected' : '' }}>Exchange</option>
                </select>
            </div>
            <div style="flex:1; min-width: 150px;">
                <select name="status" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','rejected','received_restocked','received_discarded','completed'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }} style="text-transform:capitalize;">{{ str_replace('_',' ', $st) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2; min-width: 200px;">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search order# or item" class="filter-input-rounded" style="width:100%;">
            </div>
            
            <div style="width: 100%; display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn-filter" style="width: auto; min-width: 120px;">Filter</button>
                <a href="{{ route('inventory.admin.returns-exchange.index') }}" class="btn-reset" style="width: auto; min-width: 100px;">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 60px;">Image</th>
                    <th>Order Details</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Evidence</th>
                    <th>Admin Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if(isset($productImages[$req->order_id]))
                                <img src="{{ asset('storage/' . $productImages[$req->order_id]) }}" 
                                     alt="Product" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                            @else
                                <img src="{{ asset('assets/img/no image/no_image.png') }}" 
                                     alt="Default" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                @if($req->order)
                                    <a href="{{ route('inventory.admin.orders.show', $req->order) }}" style="color:#490d59;font-weight:700;text-decoration:none;">{{ $req->order->order_number ?? '—' }}</a>
                                @else
                                    <span style="color:#6b7280;">—</span>
                                @endif
                                <span style="display:inline-block;width:fit-content;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:{{ $req->type === 'return' ? '#fef3c7' : '#e0e7ff' }};color:{{ $req->type === 'return' ? '#92400e' : '#3730a3' }};text-transform:capitalize;">
                                    {{ $req->type }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $req->order->item_name ?? '—' }}</td>
                        <td>{{ $req->order->quantity ?? 1 }}</td>
                        <td>
                            <span style="font-size:12px;text-transform:capitalize;color:#4b5563;">{{ str_replace('_',' ', $req->status) }}</span>
                        </td>
                        <td>{{ $req->reason }}</td>
                        <td>
                            @if($req->photo_path)
                                <a href="{{ asset('storage/'.$req->photo_path) }}" target="_blank" style="display:block;width:40px;height:40px;">
                                    <img src="{{ asset('storage/'.$req->photo_path) }}" alt="Evidence" style="width:100%;height:100%;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                </a>
                            @else
                                <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="No Evidence" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                            @endif
                        </td>
                        <td>
                            <div style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $req->admin_notes }}">
                                {{ $req->admin_notes ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('inventory.admin.returns-exchange.show', $req) }}" class="btn-action-sm btn-view">
                                    <i class="fas fa-eye" style="margin-right:4px;"></i> View
                                </a>

                                @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('inventory.admin.returns-exchange.approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-approve" title="Approve">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('inventory.admin.returns-exchange.deny', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-deny" title="Deny">Deny</button>
                                    </form>
                                @endif

                                @if(in_array($req->status, ['approved', 'received_restocked', 'received_discarded']))
                                    @if($req->type === 'return')
                                        @if($req->status === 'approved')
                                            <form method="POST" action="{{ route('inventory.admin.returns-exchange.receive', $req) }}" style="display:flex;gap:6px;">
                                                @csrf
                                                <input type="hidden" name="admin_notes" value="Auto-marked from list">
                                                <button type="submit" name="action" value="restock" class="btn-action-sm btn-approve" title="Mark Received & Restock">Restock</button>
                                                <button type="submit" name="action" value="discard" class="btn-action-sm btn-deny" title="Mark Received & Discard">Discard</button>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('inventory.admin.returns-exchange.refund', $req) }}" onsubmit="return confirm('Are you sure you want to process a refund for this request? This will initiate a Razorpay refund.');">
                                            @csrf
                                            <button type="submit" class="btn-action-sm" style="background:#e0f2fe; color:#0284c7; border:none;" title="Initiate Refund">
                                                <i class="fas fa-undo-alt" style="margin-right:4px;"></i> Refund
                                            </button>
                                        </form>
                                    @endif

                                    @if($req->type === 'exchange')
                                        <a href="{{ route('inventory.admin.returns-exchange.show', $req) }}" class="btn-action-sm btn-blue">
                                            Process Exchange
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:40px;text-align:center;color:#6b7280;">No return/exchange requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $requests->links() }}
    </div>
@endsection
