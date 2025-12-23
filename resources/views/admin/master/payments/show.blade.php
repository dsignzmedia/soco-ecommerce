@extends($layout ?? 'admin.layouts.base')

@php $routePrefix = $routePrefix ?? 'master.admin'; @endphp

@php $routePrefix = $routePrefix ?? 'master.admin'; @endphp

@section('title', 'Payment Details #' . $payment->id)

@section('content')
<style>
    .payment-header {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .payment-title h1 {
        font-size: 24px;
        font-weight: 700;
        color: #490d59;
        margin-bottom: 4px;
    }
    .payment-title p {
        margin: 0;
        color: #858796;
        font-size: 14px;
    }
    .status-badge {
        font-size: 14px;
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-paid { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-pending { background-color: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
    .status-failed { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-refunded { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    
    .detail-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        height: 100%;
        overflow: hidden;
        margin-bottom: 8px;
    }
    .detail-card-header {
        background: #f8f9fc;
        padding: 16px 20px;
        border-bottom: 1px solid #e3e6f0;
        font-weight: 700;
        color: #490d59;
        font-size: 16px;
    }
    .detail-card-body {
        padding: 20px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid #eaecf4;
        font-size: 14px;
        align-items: center;
    }
    .info-row:last-child { 
        margin-bottom: 0; 
        padding-bottom: 0;
        border-bottom: none; 
    }
    .info-label { 
        flex: 0 0 40%;
        max-width: 40%;
        color: #858796; 
        font-weight: 500; 
    }
    .info-value { 
        flex: 0 0 60%;
        max-width: 60%;
        color: #2e343a; 
        font-weight: 600; 
        text-align: left; 
    }
    .info-value.amount { font-size: 18px; color: #490d59; }
    
    .btn-back {
        background: #fff; 
        color: #490d59; 
        border: 1px solid #d1d3e2; 
        font-weight: 600; 
        padding: 8px 16px; 
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-back:hover {
        background: #f8f9fc;
        color: #2e2e2e;
        text-decoration: none;
    }

    .gateway-table { width: 100%; font-size: 13px; }
    .gateway-table th { background: #f8f9fc; color: #490d59; padding: 10px; border-bottom: 2px solid #e3e6f0; }
    .gateway-table td { padding: 10px; border-bottom: 1px solid #e3e6f0; vertical-align: top; }
    .gateway-table tr:last-child td { border-bottom: none; }
    .code-block { background: #f1f3f9; padding: 10px; border-radius: 6px; font-family: monospace; font-size: 12px; color: #e83e8c; }
</style>

<div class="container-fluid">
    
    <!-- Top Header Card -->
    <div class="payment-header">
        <div class="d-flex align-items-center">
            <a href="{{ route($routePrefix . '.payments.index') }}" class="btn-back mr-4">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="payment-title">
                <h1>Payment #{{ $payment->id }}</h1>
                <p>Transaction details and breakdown</p>
            </div>
        </div>
        <div>
            @if($payment->payment_status == 'paid')
                <span class="status-badge status-paid">Paid</span>
            @elseif($payment->payment_status == 'pending')
                <span class="status-badge status-pending">Pending</span>
            @elseif($payment->payment_status == 'failed')
                <span class="status-badge status-failed">Failed</span>
            @elseif($payment->payment_status == 'refunded')
                <span class="status-badge status-refunded">Refunded</span>
            @else
                <span class="status-badge" style="background:#edf2f7; color:#4a5568;">{{ ucfirst($payment->payment_status) }}</span>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Core Details -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-info-circle mr-2"></i> Payment Info
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-label">Gateway ID</span>
                        <span class="info-value text-break">{{ $payment->payment_id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Number</span>
                        <span class="info-value">
                            @php
                                $orderShowRoute = match($payment->product_type) {
                                    'merchandised' => 'admin.merchandise.orders.show',
                                    'back_to_school' => 'admin.back_to_school.orders.show',
                                    default => 'master.admin.orders.show'
                                };
                            @endphp
                            @if($payment->order)
                                <a href="{{ route($orderShowRoute, $payment->order->id) }}" style="color: #4e73df; text-decoration: underline;">
                                    {{ $payment->order->order_number }}
                                </a>
                            @else
                                <span class="text-danger">Not Linked</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ $payment->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time</span>
                        <span class="info-value">{{ $payment->created_at->format('h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Method & Type -->
        <div class="col-lg-4 col-md-6 mb-4">
             <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-credit-card mr-2"></i> Method & Type
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-label">Payment Method</span>
                        <span class="info-value">{{ ucfirst($payment->payment_method) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Type</span>
                        <span class="info-value">{{ ucfirst($payment->payment_type) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Product Type</span>
                        <span class="info-value">
                            @if($payment->product_type)
                                @if($payment->product_type == 'merchandised')
                                    <span style="background:#e0f2fe; color:#0369a1; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Merchandise</span>
                                @elseif($payment->product_type == 'back_to_school')
                                    <span style="background:#dcfce7; color:#166534; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Back to School</span>
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $payment->product_type)) }}
                                @endif
                            @else
                                <span style="color:#9ca3af;">N/A</span>
                            @endif
                        </span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Customer Name</span>
                        <span class="info-value">{{ optional($payment->order)->customer_name ?? 'N/A' }}</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Customer Email</span>
                        <span class="info-value">{{ optional($payment->order)->customer_email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> Financials
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-label">Subtotal / Total</span>
                        <span class="info-value">₹{{ number_format($payment->total_amount, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tax</span>
                        <span class="info-value">₹{{ number_format($payment->tax_amount, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Shipping</span>
                        <span class="info-value">₹{{ number_format($payment->shipping_cost, 2) }}</span>
                    </div>
                    <hr style="border-top: 1px dashed #e3e6f0; margin: 12px 0;">
                    <div class="info-row align-items-center">
                        <span class="info-label" style="color:#2e343a; font-weight:700;">Amount Paid</span>
                        <span class="info-value amount">₹{{ number_format($payment->amount_paid, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Response -->
    <div class="row">
        <div class="col-12">
            <div class="detail-card mb-4">
                <div class="detail-card-header">
                    <i class="fas fa-code mr-2"></i> Gateway Response Data
                </div>
                <div class="detail-card-body p-0">
                    @if(!empty($payment->payment_details))
                        <div class="table-responsive">
                            <table class="gateway-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Parameter Key</th>
                                        <th>Captured Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->payment_details as $key => $value)
                                        <tr>
                                            <td style="font-weight: 500; color: #555;">{{ $key }}</td>
                                            <td>
                                                @if(is_array($value) || is_object($value))
                                                    <div class="code-block">{{ json_encode($value, JSON_PRETTY_PRINT) }}</div>
                                                @else
                                                    <span style="color: #333;">{{ $value }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="https://img.icons8.com/ios/50/dddddd/code.png" alt="No Data" style="opacity:0.5; width: 40px; margin-bottom: 10px;">
                            <p class="text-muted mb-0">No raw gateway data available for this transaction.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
