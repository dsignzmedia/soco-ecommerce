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
            @php
                $hasExchangeOrders = isset($exchangeOrdersFormatted) && count($exchangeOrdersFormatted) > 0;
                $contentColumnClass = $hasExchangeOrders ? 'col-lg-12' : 'col-lg-9';
                $sidebarColumnClass = $hasExchangeOrders ? 'd-none' : 'col-lg-3';
            @endphp
            
            <!-- Left Sidebar - Account Menu (Hidden when exchange orders exist) -->
            <div class="{{ $sidebarColumnClass }} mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area (Full width when exchange orders exist) -->
            <div class="{{ $contentColumnClass }}">
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

                <!-- Order Items with Individual Tracking - Side by Side with Exchange Orders -->
                <div class="row">
                    <!-- Left Column: Original Orders -->
                    <div class="{{ $hasExchangeOrders ? 'col-lg-6' : 'col-lg-12' }} mb-4 mb-lg-0">
                        @foreach($order['items'] as $itemIndex => $item)
                            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; {{ $hasExchangeOrders ? 'width: 100%;' : '' }}">
                                <div class="card-body p-4" style="{{ $hasExchangeOrders ? 'width: 100%;' : '' }}">
                                    <!-- Product Info -->
                                    <div class="d-flex gap-3 mb-4">
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

                                        <div class="flex-grow-1">
                                            <h6 class="mb-2" style="font-weight: 600; color: #333; font-size: 1rem;">
                                                {{ !empty($item['name']) ? $item['name'] : 'Product Name Unavailable' }}
                                            </h6>
                                            <div class="d-flex gap-3 mt-2">
                                                <span class="text-muted small">Size: <strong>{{ $item['size'] ?? 'N/A' }}</strong></span>
                                                <span class="text-muted small">Qty: <strong>{{ $item['quantity'] }}</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item Status Header -->
                                    <h5 class="mb-4" style="font-weight: 600; color: #333;">
                                        @php
                                            $itemStatus = strtolower($item['status'] ?? 'pending');
                                            // Map 'order_placed' to 'pending' for display consistency
                                            if ($itemStatus === 'order_placed') {
                                                $itemStatus = 'pending';
                                            }
                                        @endphp
                                        @if($itemStatus === 'cancelled')
                                            Order Cancelled
                                        @elseif($itemStatus === 'delivered')
                                            Order Delivered
                                        @elseif($itemStatus === 'pending')
                                            Order Placed
                                        @else
                                            Order Processing
                                        @endif
                                    </h5>
                                    
                                    @if($itemStatus === 'cancelled')
                                        <p class="text-muted mb-4">The delivery partner was unable to deliver to your location</p>
                                    @endif

                                    <!-- Individual Status Timeline for this item -->
                                    <div class="position-relative" style="padding-left: 40px;">
                                        @php
                                            $itemStatusIndex = $item['status_index'] ?? 0;
                                        @endphp
                                        @foreach($statuses as $index => $status)
                                            @php
                                                $isCompleted = $index <= $itemStatusIndex;
                                                $isCurrent = $index === $itemStatusIndex;
                                                $circleColor = $isCompleted ? '#28a745' : '#e0e0e0';
                                                if ($itemStatus === 'cancelled' && $isCurrent) {
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
                                                            @php
                                                                $itemUpdatedAt = isset($item['updated_at']) ? $item['updated_at'] : $order['updated_at'];
                                                            @endphp
                                                            {{ $itemStatus === 'cancelled' ? 'Today, ' . date('M d', strtotime($itemUpdatedAt)) : date('D M d', strtotime($itemUpdatedAt)) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Exchange Button or Status (Shown after timeline if delivered) -->
                                        @if($itemStatus === 'delivered')
                                            @php
                                                $itemReturnRequest = isset($returnRequests) && $returnRequests->has($item['id']) ? $returnRequests[$item['id']] : null;
                                            @endphp
                                            @if($itemReturnRequest && $itemReturnRequest->status != 'pending')
                                                <div class="mb-4">
                                                    @if($itemReturnRequest->status == 'approved')
                                                        <div class="p-3 rounded" style="background-color: #ecfdf5; border: 1px solid #dcfce7; color: #065f46;">
                                                            <i class="fas fa-check-circle me-2"></i> 
                                                            <strong>{{ ucfirst($itemReturnRequest->type) }} Request Approved</strong>
                                                        </div>
                                                    @elseif(in_array($itemReturnRequest->status, ['received_restocked', 'received_discarded']))
                                                        <div class="p-3 rounded" style="background-color: #eff6ff; border: 1px solid #dbeafe; color: #1e40af;">
                                                            <i class="fas fa-box-open me-2"></i> 
                                                            <strong>Item Received</strong>
                                                        </div>
                                                    @elseif($itemReturnRequest->status == 'completed')
                                                        <div class="p-3 rounded" style="background-color: #f0f9ff; border: 1px solid #e0f2fe; color: #0369a1;">
                                                            <i class="fas fa-check-double me-2"></i> 
                                                            <strong>Exchange Request Approved</strong>
                                                        </div>
                                                    @elseif($itemReturnRequest->status == 'rejected')
                                                        <div class="p-3 rounded" style="background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b;">
                                                            <i class="fas fa-times-circle me-2"></i> 
                                                            <strong>Request Rejected</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mb-4">
                                                    @if(!in_array($item['product_type'] ?? '', ['back_to_school', 'merchandised']))
                                                        <a href="{{ route('frontend.parent.return-exchange', ['orderId' => $order['id'], 'itemId' => $item['id']]) }}" class="btn btn-outline-danger btn-sm" style="border-radius: 8px; font-weight: 600;">
                                                            Proceed to Exchange
                                                        </a>
                                                    @endif
                                                    @if($itemReturnRequest && $itemReturnRequest->status == 'pending')
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
                        @endforeach
                    </div>

                    <!-- Right Column: Exchange Orders -->
                    <div class="{{ $hasExchangeOrders ? 'col-lg-6' : 'd-none' }}">
                        @if(isset($exchangeOrdersFormatted) && count($exchangeOrdersFormatted) > 0)
                            @foreach($exchangeOrdersFormatted as $exchangeItem)
                                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background-color: #f8f9ff; width: 100%;">
                                    <div class="card-body p-4" style="width: 100%;">
                                        <!-- Exchange Order Header -->
                                        <div class="mb-3 pb-2 border-bottom" style="border-color: #e0e0e0 !important;">
                                            <h5 class="mb-0" style="font-weight: 600; color: #490D59;">
                                                <i class="fas fa-exchange-alt me-2"></i>Exchange Order Tracking
                                            </h5>
                                            <span class="text-muted small">Order #{{ $exchangeItem['order_number'] }}</span>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="d-flex gap-3 mb-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                @if(isset($exchangeItem['image']) && $exchangeItem['image'])
                                                    <img src="{{ $exchangeItem['image'] }}" alt="{{ $exchangeItem['name'] }}" 
                                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e0e0e0;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                        style="width: 80px; height: 80px; border: 1px solid #e0e0e0;">
                                                        <i class="fas fa-image text-muted fa-2x"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1">
                                                <h6 class="mb-2" style="font-weight: 600; color: #333; font-size: 1rem;">
                                                    {{ !empty($exchangeItem['name']) ? $exchangeItem['name'] : 'Product Name Unavailable' }}
                                                </h6>
                                                <div class="d-flex gap-3 mt-2">
                                                    <span class="text-muted small">Size: <strong>{{ $exchangeItem['size'] ?? 'N/A' }}</strong></span>
                                                    <span class="text-muted small">Qty: <strong>{{ $exchangeItem['quantity'] }}</strong></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Exchange Order Status Header -->
                                        <h5 class="mb-4" style="font-weight: 600; color: #333;">
                                            @php
                                                $exchangeStatus = strtolower($exchangeItem['status'] ?? 'pending');
                                                // Map 'order_placed' to 'pending' for display consistency
                                                if ($exchangeStatus === 'order_placed') {
                                                    $exchangeStatus = 'pending';
                                                }
                                            @endphp
                                            @if($exchangeStatus === 'cancelled')
                                                Order Cancelled
                                            @elseif($exchangeStatus === 'delivered')
                                                Order Delivered
                                            @elseif($exchangeStatus === 'pending')
                                                Order Placed
                                            @else
                                                Order Processing
                                            @endif
                                        </h5>

                                        <!-- Exchange Order Status Timeline -->
                                        <div class="position-relative" style="padding-left: 40px;">
                                            @php
                                                $exchangeStatusIndex = $exchangeItem['status_index'] ?? 0;
                                            @endphp
                                            @foreach($statuses as $index => $status)
                                                @php
                                                    $isCompleted = $index <= $exchangeStatusIndex;
                                                    $isCurrent = $index === $exchangeStatusIndex;
                                                    $isOrderPlaced = strtolower($status['label']) === 'order placed';
                                                    
                                                    // Green color for all completed statuses in exchange orders
                                                    if ($isCompleted) {
                                                        $circleColor = '#28a745'; // Green color
                                                        $lineColor = '#28a745'; // Green for timeline line
                                                    } else {
                                                        $circleColor = '#e0e0e0'; // Grey for incomplete
                                                        $lineColor = '#e0e0e0';
                                                    }
                                                    
                                                    if ($exchangeStatus === 'cancelled' && $isCurrent) {
                                                        $circleColor = '#dc3545';
                                                        $lineColor = '#dc3545';
                                                    }
                                                @endphp
                                                
                                                <div class="mb-4 position-relative">
                                                    <!-- Timeline Line -->
                                                    @if(!$loop->last)
                                                        @php
                                                            // Determine line color: Green for all completed statuses
                                                            $nextStatusIndex = $index + 1;
                                                            $nextIsCompleted = $nextStatusIndex <= $exchangeStatusIndex;
                                                            
                                                            if ($isCompleted && $nextIsCompleted) {
                                                                // Green line for completed statuses
                                                                $lineColorToUse = '#28a745';
                                                            } else {
                                                                // Grey line for incomplete sections
                                                                $lineColorToUse = '#e0e0e0';
                                                            }
                                                        @endphp
                                                        <div style="position: absolute; left: -28px; top: 20px; bottom: -24px; width: 2px; background-color: {{ $lineColorToUse }};"></div>
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
                                                                        $desc = 'We have received your exchange order';
                                                                        break;
                                                                    case 'processing':
                                                                        $icon = 'fa-cog';
                                                                        $desc = 'We are preparing your exchange order';
                                                                        break;
                                                                    case 'packed':
                                                                        $icon = 'fa-box-open';
                                                                        $desc = 'Your exchange order is packed and ready';
                                                                        break;
                                                                    case 'shipped':
                                                                        $icon = 'fa-shipping-fast';
                                                                        $desc = 'Your exchange order is on the way';
                                                                        break;
                                                                    case 'delivered':
                                                                        $icon = 'fa-home';
                                                                        $desc = 'Exchange package delivered';
                                                                        break;
                                                                }
                                                            @endphp
                                                            <i class="fas {{ $icon }} me-2" style="color: {{ $isCompleted ? '#28a745' : '#999' }};"></i> {{ $status['label'] }}
                                                        </h6>
                                                        <p class="text-muted small mb-1" style="font-size: 0.85rem;">{{ $desc }}</p>
                                                        @if($isCurrent)
                                                            <p class="text-muted small mb-0">
                                                                {{ $exchangeStatus === 'cancelled' ? 'Today, ' . date('M d', strtotime($exchangeItem['updated_at'])) : date('D M d', strtotime($exchangeItem['updated_at'])) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
