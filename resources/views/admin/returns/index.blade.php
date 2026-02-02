@extends('admin.layouts.base')

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
            flex-wrap: wrap; /* Allow wrap on small screens but keep side-by-side on large */
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
            min-width: 150px; /* Reduced min-width slightly to fit more */
            flex: 1; /* Allow them to grow */
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

        /* Action Buttons */
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
            transition: all 0.2s;
        }
        .btn-action-sm:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        .btn-view { background: #fff; border: 1px solid #d0d5dd; color: #344054; }
        .btn-view:hover { background: #f9fafb; border-color: #490d59; color: #490d59; }
        
        .btn-approve { background: #dcfce7; color: #027a48; padding: 6px 10px; border: 1px solid #bbf7d0; }
        .btn-approve:hover { background: #bbf7d0; border-color: #86efac; }
        
        .btn-deny { background: #fee4e2; color: #b42318; padding: 6px 10px; border: 1px solid #fecaca; }
        .btn-deny:hover { background: #fecaca; border-color: #fca5a5; color: #991b1b; }
        
        .btn-blue { background: #e0f2fe; color: #026aa7; border: 2px solid #0284c7; }
        .btn-blue:hover { background: #bae6fd; border-color: #7dd3fc; color: #0369a1; }
        
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow-x: auto;
            overflow-y: visible;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        /* Ensure table cells don't allow horizontal overflow of hover cards */
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 13px; vertical-align: middle; }
        th { background: #f9fafb; font-weight: 600; color: #475467; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }

        /* Specific modifications requested */
        th:first-child, td:first-child {
            padding-left: 32px; /* Increased padding */
        }
    </style>

    <div class="filters-card">
        <form method="GET" action="{{ route('master.admin.returns-exchange.index') }}" class="filter-form-grid">
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
            <div style="flex:1; min-width: 150px;">
                <select name="school_id" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 150px;">
                <select name="grade" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Grades</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" @selected(($filters['grade'] ?? '') === $grade)>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2; min-width: 200px;">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search order# or item" class="filter-input-rounded" style="width:100%;">
            </div>
            
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('master.admin.returns-exchange.index') }}" class="btn-reset">Reset</a>
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
                                        href="{{ route('master.admin.orders.show', $req->order) }}"
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
                        <td style="position: relative; z-index: 1;">{{ $req->order->item_name ?? '—' }}</td>
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
                                <a href="{{ route('master.admin.returns-exchange.show', $req) }}" class="btn-action-sm btn-view">
                                    <i class="fas fa-eye" style="margin-right:4px;"></i> View
                                </a>

                                @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('master.admin.returns-exchange.approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-approve" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('master.admin.returns-exchange.deny', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-deny" title="Deny">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($req->status, ['approved', 'received_restocked', 'received_discarded']))
                                    @if($req->type === 'return')
                                        @if($req->status === 'approved')
                                            <form method="POST" action="{{ route('master.admin.returns-exchange.receive', $req) }}" style="display:flex;gap:6px;">
                                                @csrf
                                                <input type="hidden" name="admin_notes" value="Auto-marked from list">
                                                <button type="submit" name="action" value="restock" class="btn-action-sm btn-approve" title="Mark Received & Restock">Restock</button>
                                                <button type="submit" name="action" value="discard" class="btn-action-sm btn-deny" title="Mark Received & Discard">Discard</button>
                                            </form>
                                        @endif
                                        
                                        <!-- Refund Button -->
                                        <form method="POST" action="{{ route('master.admin.returns-exchange.refund', $req) }}" onsubmit="return confirm('Are you sure you want to process a refund for this request? This will initiate a Razorpay refund.');">
                                            @csrf
                                            <button type="submit" class="btn-action-sm" style="background:#e0f2fe; color:#0284c7; border:none;" title="Initiate Refund">
                                                <i class="fas fa-undo-alt" style="margin-right:4px;"></i> Refund
                                            </button>
                                        </form>
                                    @endif

                                    @if($req->type === 'exchange')
                                        <a href="{{ route('master.admin.returns-exchange.show', $req) }}" class="btn-action-sm btn-blue" style="border: 2px solid #0284c7;">
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

    <div class="pagination-container">
        {{ $requests->links() }}
    </div>
@endsection

@push('styles')
<style>
    /* Custom Pagination Styling (Same as Orders Page) */
    .pagination-container {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }
    
    /* Hide the mobile view (the 'Previous' 'Next' text links on the left) */
    .pagination-container nav > div:first-child {
        display: none !important;
    }

    /* Ensure the desktop view takes full width */
    .pagination-container nav > div:last-child {
        display: flex !important;
        justify-content: space-between;
        width: 100%;
        align-items: center;
    }

    /* The "Showing x to y" text */
    .pagination-container p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* Pagination Buttons Container (usually a div with shadow in Tailwind) */
    /* We reset the shadow and rounded corners of the container to apply them to buttons instead */
    .pagination-container nav span[class*="shadow-sm"],
    .pagination-container nav div[class*="shadow-sm"] {
        box-shadow: none !important;
        display: inline-flex;
        gap: 4px; /* Space between buttons */
    }

    /* Pagination Styling - Enhanced (Same as Orders) */
    .pagination {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 0;
        padding: 0;
        list-style: none;
        margin-left: 0;
        margin-right: 0;
    }
    
    .pagination > * {
        margin: 0 !important;
    }
    
    /* All pagination links and spans */
    .pagination-container nav a, 
    .pagination-container nav span[aria-disabled], 
    .pagination-container nav span[aria-current="page"] > span,
    .pagination a,
    .pagination span,
    .pagination .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 42px !important;
        height: 42px !important;
        padding: 0 14px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        color: #6b7280 !important;
        margin: 0 2px !important;
        cursor: pointer;
        box-sizing: border-box !important;
    }
    
    .pagination-container nav a:hover,
    .pagination a:hover,
    .pagination .page-link:hover {
        background-color: #f9fafb !important;
        border-color: #490d59 !important;
        color: #490d59 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(73, 13, 89, 0.15) !important;
    }

    /* Active Page Styles */
    .pagination-container nav span[aria-current="page"] > span,
    .pagination span[aria-current="page"],
    .pagination .active span,
    .pagination .page-item.active .page-link,
    .pagination .page-link.active {
        background-color: #490d59 !important;
        border-color: #490d59 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    /* Disabled State (Previous/Next arrows when inactive) */
    .pagination-container nav span[aria-disabled],
    .pagination span[aria-disabled="true"],
    .pagination .page-item.disabled .page-link,
    .pagination .page-link.disabled {
        background-color: #f3f4f6 !important;
        color: #9ca3af !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
        pointer-events: none;
    }
    
    /* Previous/Next buttons */
    .pagination-container nav a[rel="prev"],
    .pagination-container nav a[rel="next"],
    .pagination a[rel="prev"],
    .pagination a[rel="next"],
    .pagination .page-link[rel="prev"],
    .pagination .page-link[rel="next"] {
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        color: #490d59 !important;
        font-weight: 600 !important;
        padding: 0 16px !important;
        min-width: auto !important;
    }
    
    .pagination-container nav a[rel="prev"]:hover,
    .pagination-container nav a[rel="next"]:hover,
    .pagination a[rel="prev"]:hover,
    .pagination a[rel="next"]:hover {
        background-color: #490d59 !important;
        color: #ffffff !important;
        border-color: #490d59 !important;
    }
    
    /* Fix for arrows (SVG alignment) */
    .pagination-container nav svg,
    .pagination svg {
        width: 16px;
        height: 16px;
    }
    
    /* Responsive pagination */
    @media (max-width: 768px) {
        .pagination {
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        
        .pagination a,
        .pagination span,
        .pagination .page-link,
        .pagination-container nav a,
        .pagination-container nav span {
            min-width: 38px !important;
            height: 38px !important;
            font-size: 13px !important;
            padding: 0 10px !important;
        }
    }
</style>
@endpush