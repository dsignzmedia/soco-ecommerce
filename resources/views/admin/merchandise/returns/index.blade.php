@extends('admin.layouts.merchandise')

@section('title', 'Exchanges | The Skool Store')
@section('page_heading', 'Exchanges')
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
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border-radius: 8px !important;
            font-size: 13px;
            color: #374151;
            outline: none;
            background-color: #fff;
            font-family: inherit;
            min-width: 150px;
            flex: 1;
        }
        select.filter-input-rounded {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
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
        .btn-blue { background: #e0f2fe; color: #026aa7; border: 2px solid #0284c7; }
        .btn-blue:hover { background: #bae6fd; border-color: #7dd3fc; color: #0369a1; }
        
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
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
        
        /* Ensure table cells don't allow horizontal overflow of hover cards */
        .table-wrap {
            overflow-y: visible;
        }
        .table-wrap table {
            table-layout: auto;
        }
        /* Order hover card */
        .order-hover-card {
            position: relative;
            display: inline-block;
            align-items: center;
            gap: 8px;
        }
        .order-hover-card .hover-card {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            min-width: 250px;
            max-width: 280px;
            z-index: 1000;
            margin-top: 4px;
            white-space: nowrap;
        }
        
        /* Prevent hover card from extending beyond table cell */
        table td {
            position: relative;
            overflow: visible;
            vertical-align: top;
        }
        
        /* Constrain ORDER DETAILS column to prevent hover card overflow */
        table td:nth-child(3) {
            overflow: visible;
            position: relative;
            max-width: 200px;
            min-width: 150px;
        }
        
        /* Ensure hover card stays within ORDER DETAILS column boundaries */
        table td:nth-child(3) .order-hover-card {
            display: inline-block;
            max-width: 100%;
            position: relative;
        }
        
        /* Constrain hover card width and prevent horizontal overflow */
        table td:nth-child(3) .order-hover-card .hover-card {
            max-width: 280px;
            word-wrap: break-word;
            white-space: normal;
            left: 0 !important;
            right: auto !important;
        }
        
        /* Ensure ITEM column has proper z-index and spacing */
        table td:nth-child(4) {
            position: relative;
            z-index: 1;
            padding-left: 12px;
            min-width: 150px;
        }
        .order-hover-card .hover-card .row {
            display: grid;
            grid-template-columns: 90px 1fr;
            gap: 6px;
            align-items: baseline;
            margin-bottom: 6px;
        }
        .order-hover-card .hover-card .row:last-child {
            margin-bottom: 0;
        }
        .order-hover-card .hover-card .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }
        .order-hover-card .hover-card .value {
            font-size: 14px;
            color: #111827;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .order-hover-card:hover .hover-card {
            display: block;
        }
    </style>

    <div class="filters-card">
        <form method="GET" action="{{ route('admin.merchandise.returns-exchange.index') }}" class="filter-form-grid">
            <div style="flex:1; min-width: 150px;">
                <select name="type[]" multiple class="filter-input-rounded" style="width:100%;" placeholder="Select Type">
                    <option value="">All Types</option>
                    <option value="return" @selected(in_array('return', (array)($filters['type'] ?? [])))>Return</option>
                    <option value="exchange" @selected(in_array('exchange', (array)($filters['type'] ?? [])))>Exchange</option>
                </select>
            </div>
            <div style="flex:1; min-width: 150px;">
                <select name="status[]" multiple class="filter-input-rounded" style="width:100%;" placeholder="Select Status">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','rejected','received_restocked','received_discarded','completed'] as $st)
                        <option value="{{ $st }}" @selected(in_array($st, (array)($filters['status'] ?? []))) style="text-transform:capitalize;">{{ str_replace('_',' ', $st) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2; min-width: 200px;">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search order# or item" class="filter-input-rounded" style="width:100%;">
            </div>
            
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('admin.merchandise.returns-exchange.index') }}" class="btn-reset">Reset</a>
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
                            @if(isset($productImages[$req->order_id]) && !empty($productImages[$req->order_id]))
                                <img src="{{ asset('storage/' . $productImages[$req->order_id]) }}" 
                                     alt="Product" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 6px; display: none; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px; border: 1px solid #e5e7eb;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @else
                                <div style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px; border: 1px solid #e5e7eb;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td style="position: relative; overflow: visible;">
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                @if($req->order)
                                    @php
                                        $tooltipSchool = $req->order->school?->name ?? '—';
                                        $tooltipGrade = $req->order->grade ?? '—';
                                        $tooltipStudent = $req->order->student_name
                                            ?? ($req->order->student->name ?? null)
                                            ?? '—';
                                    @endphp
                                    <div class="order-hover-card" style="display: inline-block;">
                                    <a
                                        href="{{ route('admin.merchandise.orders.show', $req->order) }}"
                                            style="color:#490d59;font-weight:700;text-decoration:none;"
                                    >{{ $req->order->order_number ?? '—' }}</a>
                                        <div class="hover-card">
                                            <div class="row">
                                                <span class="label">School:</span>
                                                <span class="value">{{ $tooltipSchool }}</span>
                                            </div>
                                            <div class="row">
                                                <span class="label">Grade:</span>
                                                <span class="value">{{ $tooltipGrade }}</span>
                                            </div>
                                            <div class="row">
                                                <span class="label">Student:</span>
                                                <span class="value">{{ $tooltipStudent }}</span>
                                            </div>
                                        </div>
                                    </div>
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
                                <a href="{{ route('admin.merchandise.returns-exchange.show', $req) }}" class="btn-action-sm btn-view">
                                    <i class="fas fa-eye" style="margin-right:4px;"></i> View
                                </a>

                                @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('admin.merchandise.returns-exchange.approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-approve" title="Approve">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.merchandise.returns-exchange.deny', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-deny" title="Deny">Deny</button>
                                    </form>
                                @endif

                                @if(in_array($req->status, ['approved', 'received_restocked', 'received_discarded']))
                                    @if($req->type === 'return')
                                        @if($req->status === 'approved')
                                            <form method="POST" action="{{ route('admin.merchandise.returns-exchange.receive', $req) }}" style="display:flex;gap:6px;">
                                                @csrf
                                                <input type="hidden" name="admin_notes" value="Auto-marked from list">
                                                <button type="submit" name="action" value="restock" class="btn-action-sm btn-approve" title="Mark Received & Restock">Restock</button>
                                                <button type="submit" name="action" value="discard" class="btn-action-sm btn-deny" title="Mark Received & Discard">Discard</button>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.merchandise.returns-exchange.refund', $req) }}" onsubmit="return confirm('Are you sure you want to process a refund for this request? This will initiate a Razorpay refund.');">
                                            @csrf
                                            <button type="submit" class="btn-action-sm" style="background:#e0f2fe; color:#0284c7; border:none;" title="Initiate Refund">
                                                <i class="fas fa-undo-alt" style="margin-right:4px;"></i> Refund
                                            </button>
                                        </form>
                                    @endif

                                    @if($req->type === 'exchange')
                                        <a href="{{ route('admin.merchandise.returns-exchange.show', $req) }}" class="btn-action-sm btn-blue" style="border: 2px solid #0284c7;">
                                            <i class="fas fa-exchange-alt" style="margin-right: 4px;"></i> Process Exchange
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:40px;text-align:center;color:#6b7280;">No exchange requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $requests->links() }}
    </div>
@endsection

