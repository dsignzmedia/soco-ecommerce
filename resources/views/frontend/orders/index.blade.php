@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px; border-bottom: 1px solid #d0d0d0;">
    <div class="container" style="padding: 20px;">
        <div class="breadcumb-menu-wrap" style="margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li>My Orders</li>
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
                <!-- Page Header -->
                <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <h2 class="mb-0" style="color: #ffffff; font-weight: 600; font-size: 1.75rem;">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </h2>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #28a745;">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(count($orders) > 0)
                    <!-- Orders List -->
                    <div class="orders-list">
                        @foreach($orders as $order)
                            @foreach($order['items'] as $item)
                                @php
                                    // Determine route - if exchange order, show only that exchange order's tracking
                                    if (isset($item['is_exchange_order']) && $item['is_exchange_order']) {
                                        // For exchange orders, show only that exchange order's tracking
                                        $trackRoute = route('frontend.parent.track-order', ['orderId' => $item['id'], 'itemId' => $item['id']]);
                                    } else {
                                        // For regular orders, use normal tracking
                                        $trackRoute = route('frontend.parent.track-order', ['orderId' => $order['id'], 'itemId' => $item['id']]);
                                    }
                                @endphp
                                <a href="{{ $trackRoute }}" class="card shadow-sm border-0 mb-3 position-relative text-decoration-none" style="border-radius: 12px; transition: all 0.3s; display: block;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(73, 13, 89, 0.15)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">


                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-3">
                                            <!-- Product Image -->
                                            <div class="col-auto flex-shrink-0">
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

                                            <!-- Order Details -->
                                            <div class="col" style="min-width: 0; padding-right: 30px;">
                                                <!-- Exchange Order Badge - Mobile Responsive -->
                                                @if(isset($item['is_exchange_order']) && $item['is_exchange_order'])
                                                    <div class="mb-2">
                                                        <span class="badge exchange-badge" style="background-color: #28a745; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 600; display: inline-block; position: relative;">
                                                            <i class="fas fa-exchange-alt me-1"></i> Exchange
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                <!-- Status Badge (Commented out in original) -->
                                                @php
                                                    $statusColor = '#6c757d';
                                                    $statusText = ucfirst($order['status']);
                                                    
                                                    if ($order['status'] === 'delivered') {
                                                        $statusColor = '#28a745';
                                                        $statusText = 'Delivered on ' . date('M d, Y', strtotime($order['created_at']));
                                                    } elseif ($order['status'] === 'cancelled') {
                                                        $statusColor = '#dc3545';
                                                        $statusText = 'Cancelled Today, ' . date('M d', strtotime($order['created_at']));
                                                    } elseif ($order['status'] === 'pending') {
                                                        $statusColor = '#ffc107';
                                                        $statusText = 'Order Confirmed, ' . date('M d', strtotime($order['created_at']));
                                                    } elseif ($order['status'] === 'shipped') {
                                                        $statusColor = '#17a2b8';
                                                        $statusText = 'Shipped on ' . date('M d, Y', strtotime($order['created_at']));
                                                    }
                                                    
                                                    // Check for exchange status
                                                    if(isset($item['return_request']) && $item['return_request']) {
                                                        $req = $item['return_request'];
                                                        if ($req['status'] === 'approved') {
                                                            $statusColor = '#28a745';
                                                            $statusText = ucfirst($req['type']) . ' Approved';
                                                        } elseif ($req['status'] === 'pending') {
                                                            $statusColor = '#ffc107';
                                                            $statusText = ucfirst($req['type']) . ' Requested';
                                                        }
                                                    }
                                                @endphp
                                                


                                                <!-- Product Name - Mobile Responsive with proper wrapping -->
                                                <h6 class="mb-2 product-name-responsive" style="font-weight: 600; color: #333; font-size: 1rem; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.4;">
                                                    <span title="{{ trim($item['name']) !== '' ? $item['name'] : 'Product Name Unavailable' }}">
                                                        {{ trim($item['name']) !== '' ? $item['name'] : 'Product Name Unavailable' }}
                                                    </span>
                                                </h6>

                                                <!-- Simple Quantity Display -->
                                                <div class="mb-2 position-relative" style="z-index: 2;">
                                                    <span class="text-muted small me-3">Size: <strong>{{ $item['size'] ?? 'N/A' }}</strong></span>
                                                    <span class="text-muted small">Qty: <strong>{{ $item['quantity'] }}</strong></span>
                                                </div>

                                            </div>

                                            <!-- Arrow Icon -->
                                            <div class="col-auto flex-shrink-0">
                                                <i class="fas fa-chevron-right" style="color: #999; font-size: 1.2rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="card shadow-sm border-0" style="border-radius: 16px;">
                        <div class="card-body text-center py-5">
                            <div style="font-size: 5rem; color: #e0e0e0; margin-bottom: 20px;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <h4 class="mb-3" style="color: #666;">No orders yet</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('frontend.parent.store') }}" class="btn btn-lg" 
                                style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); color: #ffffff; border: none; border-radius: 12px; padding: 14px 32px; font-weight: 600;">
                                <i class="fas fa-shopping-cart me-2"></i> Start Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </section>

    <style>
        /* Mobile Responsive Styles for Order Cards */
        .product-name-responsive {
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            hyphens: auto;
            max-width: 100%;
        }
        
        /* Remove margin-top from row elements */
        .orders-list .row > * {
            margin-top: 0 !important;
        }
        
        .orders-list .row {
            margin-top: 0 !important;
        }
        
        /* Fix badge positioning - ensure it doesn't overlap */
        .exchange-badge {
            position: relative !important;
            display: inline-block !important;
            margin-bottom: 8px !important;
            z-index: 1 !important;
        }
        
        /* Override any absolute positioning that might be causing overlap */
        .orders-list .badge {
            position: relative !important;
        }
        
        @media (max-width: 768px) {
            .orders-list .card-body {
                padding: 12px !important;
            }
            
            .orders-list .card-body .row {
                margin: 0 !important;
            }
            
            .orders-list .product-name-responsive {
                font-size: 0.9rem !important;
                margin-right: 0 !important;
                padding-right: 0 !important;
                word-break: break-word;
                margin-top: 4px !important;
            }
            
            .orders-list .exchange-badge {
                font-size: 0.65rem !important;
                padding: 3px 8px !important;
                margin-bottom: 8px !important;
                display: block !important;
                width: fit-content !important;
            }
            
            .orders-list img {
                width: 70px !important;
                height: 70px !important;
            }
            
            .orders-list .bg-light {
                width: 70px !important;
                height: 70px !important;
            }
            
            .orders-list .col {
                padding-right: 25px !important;
            }
        }
        
        @media (max-width: 576px) {
            .orders-list .product-name-responsive {
                font-size: 0.85rem !important;
                line-height: 1.3 !important;
            }
            
            .orders-list .exchange-badge {
                font-size: 0.6rem !important;
                padding: 2px 6px !important;
            }
            
            .orders-list img {
                width: 60px !important;
                height: 60px !important;
            }
            
            .orders-list .bg-light {
                width: 60px !important;
                height: 60px !important;
            }
            
            .orders-list .col {
                padding-right: 20px !important;
            }
        }
    </style>

@endsection
