@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<style>
    @media (max-width: 768px) {
        .breadcrumb-wrapper { padding-top: 20px !important; }
        .space-top { padding-top: 30px !important; }
        .space-extra-bottom { padding-bottom: 30px !important; }
    }
</style>

<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px; border-bottom: 1px solid #d0d0d0;">
    <div class="container" style="padding: 20px;">
        <div class="breadcumb-menu-wrap" style="margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li><a href="{{ route('frontend.parent.orders') }}">My Orders</a></li>
                <li>Order #SOCO-{{ $order['id'] }}</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
                <!-- Back Button & Header -->
                <div class="d-flex align-items-center mb-4 flex-wrap gap-3">
                    <a href="{{ route('frontend.parent.orders') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 16px;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="mb-0 text-truncate" style="font-weight: 600; color: #333; font-size: 1.5rem; max-width: 200px;">Order Details</h2>
                    <a href="{{ route('frontend.contact') }}" class="ms-auto btn btn-outline-primary" style="border-radius: 10px; padding: 10px 20px;">
                        Help
                    </a>
                </div>

                <!-- Order ID Header -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <span class="text-muted small">Order #SOCO-{{ $order['id'] }}</span>
                    </div>
                    <button class="btn btn-sm btn-link p-0" onclick="copyOrderId()" style="color: #490D59; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-copy me-1"></i> Copy
                    </button>
                </div>

                <!-- Order Items -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        @foreach($order['items'] as $item)
                            <div class="d-flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-bottom' : '' }}">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" 
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e0e0e0;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                            style="width: 80px; height: 80px; border: 1px solid #e0e0e0;">
                                            <i class="fas fa-image text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>

                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                                        <div class="mb-2 mb-sm-0">
                                            <h6 class="mb-2" style="font-weight: 600; color: #333; font-size: 1rem;">
                                                {{ !empty($item['name']) ? $item['name'] : 'Product Name Unavailable' }}
                                            </h6>
                                            <div class="d-flex gap-3 mt-2">
                                                <span class="text-muted small">Size: <strong>{{ $item['size'] ?? 'N/A' }}</strong></span>
                                                <span class="text-muted small">Qty: <strong>{{ $item['quantity'] }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order ID & Status Combined -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <!-- Order Status -->
                        <h5 class="mb-4" style="font-weight: 600; color: #333;">
                            @if($order['status'] === 'cancelled')
                                Order Cancelled
                            @elseif($order['status'] === 'delivered')
                                Order Delivered
                            @else
                                Order Processing
                            @endif
                        </h5>
                        
                        @if($order['status'] === 'cancelled')
                            <p class="text-muted mb-4">The delivery partner was unable to deliver to your location</p>
                        @endif

                        <!-- Status Timeline -->
                        <div class="position-relative" style="padding-left: 40px;">
                            @foreach($statuses as $index => $status)
                                @php
                                    $isCompleted = $index <= $currentStatusIndex;
                                    $isCurrent = $index === $currentStatusIndex;
                                    $circleColor = $isCompleted ? '#28a745' : '#e0e0e0';
                                    if ($order['status'] === 'cancelled' && $isCurrent) {
                                        $circleColor = '#dc3545';
                                    }
                                @endphp
                                
                                <div class="mb-4 position-relative">
                                    <!-- Timeline Line -->
                                    @if(!$loop->last)
                                        <div style="position: absolute; left: -28px; top: 20px; bottom: -24px; width: 2px; background-color: {{ $isCompleted ? '#28a745' : '#e0e0e0' }};"></div>
                                    @endif
                                    
                                    <!-- Status Circle -->
                                    <div style="position: absolute; left: -35px; top: 0; width: 16px; height: 16px; border-radius: 50%; background-color: {{ $circleColor }}; border: 3px solid #ffffff; box-shadow: 0 0 0 2px {{ $circleColor }};"></div>
                                    
                                    <!-- Status Content -->
                                    <div>
                                        <h6 class="mb-1" style="font-weight: 600; color: {{ $isCompleted ? '#333' : '#999' }};">
                                            @php
                                                $icon = 'fa-circle';
                                                $desc = '';
                                                switch(strtolower($status['label'])) {
                                                    case 'order placed':
                                                        $icon = 'fa-clipboard-check';
                                                        $desc = 'We have received your order';
                                                        break;
                                                    case 'processing':
                                                        $icon = 'fa-cog';
                                                        $desc = 'We are preparing your order';
                                                        break;
                                                    case 'packed':
                                                        $icon = 'fa-box-open';
                                                        $desc = 'Your order is packed and ready';
                                                        break;
                                                    case 'shipped':
                                                        $icon = 'fa-shipping-fast';
                                                        $desc = 'Your order is on the way';
                                                        break;
                                                    case 'delivered':
                                                        $icon = 'fa-home';
                                                        $desc = 'Package delivered';
                                                        break;
                                                }
                                            @endphp
                                            <i class="fas {{ $icon }} me-2"></i> {{ $status['label'] }}
                                        </h6>
                                        <p class="text-muted small mb-1" style="font-size: 0.85rem;">{{ $desc }}</p>
                                        @if($isCurrent)
                                            <p class="text-muted small mb-0">
                                                {{ $order['status'] === 'cancelled' ? 'Today, ' . date('M d', strtotime($order['updated_at'])) : date('D M d', strtotime($order['updated_at'])) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Return / Exchange Button or Status (Shown after timeline if delivered) -->
                            @if($order['status'] === 'delivered')
                                @if(isset($returnRequest) && $returnRequest->status != 'pending')
                                    <div class="mb-4">
                                        @if($returnRequest->status == 'approved')
                                            <div class="p-3 rounded" style="background-color: #ecfdf5; border: 1px solid #dcfce7; color: #065f46;">
                                                <i class="fas fa-check-circle me-2"></i> 
                                                <strong>{{ ucfirst($returnRequest->type) }} Request Approved</strong>
                                            </div>
                                        @elseif(in_array($returnRequest->status, ['received_restocked', 'received_discarded']))
                                            <div class="p-3 rounded" style="background-color: #eff6ff; border: 1px solid #dbeafe; color: #1e40af;">
                                                <i class="fas fa-box-open me-2"></i> 
                                                <strong>Return Received</strong>
                                            </div>
                                        @elseif($returnRequest->status == 'completed')
                                            <div class="p-3 rounded" style="background-color: #f0f9ff; border: 1px solid #e0f2fe; color: #0369a1;">
                                                <i class="fas fa-check-double me-2"></i> 
                                                <strong>Exchange Completed</strong>
                                            </div>
                                        @elseif($returnRequest->status == 'rejected')
                                            <div class="p-3 rounded" style="background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b;">
                                                <i class="fas fa-times-circle me-2"></i> 
                                                <strong>Request Rejected</strong>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <a href="{{ route('frontend.parent.return-exchange', ['orderId' => $order['id']]) }}" class="btn btn-outline-danger btn-sm" style="border-radius: 8px; font-weight: 600;">
                                            Proceed to Return/Exchange
                                        </a>
                                        @if(isset($returnRequest) && $returnRequest->status == 'pending')
                                            <div class="mt-2 text-warning small">
                                                <i class="fas fa-clock me-1"></i> Request Pending Approval
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rate Experience (if cancelled) -->
                @if($order['status'] === 'cancelled')
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <h6 class="mb-3" style="font-weight: 600; color: #333;">Rate your experience</h6>
                            <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #333;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-comment-dots fa-2x" style="color: #490D59;"></i>
                                    <span>How was your cancellation experience?</span>
                                </div>
                                <i class="fas fa-chevron-right" style="color: #999;"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <!-- Delivery Details -->
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h6 class="mb-3" style="font-weight: 600; color: #333;">Delivery details</h6>
                                
                                <div class="mb-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fas fa-home" style="color: #490D59; font-size: 1.2rem; margin-top: 4px;"></i>
                                        <div>
                                            <p class="mb-1" style="font-weight: 500; color: #333;">Home</p>
                                            <p class="text-muted small mb-0">{{ $order['customer_address'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fas fa-user" style="color: #490D59; font-size: 1.2rem; margin-top: 4px;"></i>
                                        <div>
                                            <p class="mb-0" style="font-weight: 500; color: #333;">{{ $order['customer_name'] }}</p>
                                            <p class="text-muted small mb-0">{{ $order['customer_phone'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Details -->
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h6 class="mb-3" style="font-weight: 600; color: #333;">Price details</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Order ID</span>
                                    <span style="font-weight: 500;">#SOCO-{{ $order['id'] }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Listing price</span>
                                    <span style="font-weight: 500;">&#8377;{{ number_format($order['subtotal'] ?? $order['total']) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tax</span>
                                    <span style="font-weight: 500;">&#8377;{{ number_format($order['tax'] ?? 0) }}</span>
                                </div>
                                
                                <hr style="margin: 10px 0; border-color: #e0e0e0;">

                                <div class="d-flex justify-content-between mb-0">
                                    <span style="font-weight: 600; color: #333;">Total amount</span>
                                    <span style="font-weight: 600; font-size: 1.1rem; color: #333;">&#8377;{{ number_format($order['total']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shop More Button -->
                <div class="card shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        <a href="{{ route('frontend.parent.store') }}" class="btn btn-primary w-100" 
                            style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); border: none; border-radius: 10px; padding: 12px; font-weight: 600;">
                            <i class="fas fa-shopping-cart me-2"></i> Shop more from SOCO
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyOrderId() {
    const orderId = 'SOCO-{{ $order["id"] }}';
    navigator.clipboard.writeText(orderId).then(() => {
        alert('Order ID copied to clipboard!');
    });
}
</script>

@endsection
