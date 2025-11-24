@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>
            <div class="col-lg-9">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="h3 mb-0">My Orders</h2>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(count($orders) > 0)
                    @foreach($orders as $order)
                        <div class="card shadow-sm rounded-4 border-0 mb-4" style="background-color: #ffffff;">
                            <div class="card-body p-4">
                                <!-- Order Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2" style="color: #490D59; font-size: 1.5rem;">Order #{{ $order['id'] }}</h5>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-calendar me-1"></i> Placed on {{ date('M d, Y', strtotime($order['created_at'])) }}
                                        </p>
                                        @if(isset($order['student_name']) && $order['student_name'])
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-user-graduate me-1"></i> For: <strong style="color: #490D59;">{{ $order['student_name'] }}</strong>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0">
                                            <strong style="color: #490D59; font-size: 1.3rem;">₹{{ number_format($order['total']) }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <!-- Order Items - Each product in separate card -->
                                <div class="mb-4">
                                    @foreach($order['items'] as $item)
                                        <div class="card mb-3 border-0" style="background-color: #f8f5ff; border-radius: 12px;">
                                            <div class="card-body p-3">
                                                <div class="d-flex gap-3 align-items-center">
                                                    <div class="flex-shrink-0">
                                                        @if(isset($item['image']) && $item['image'])
                                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e0d5f0;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border: 1px solid #e0d5f0;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-2" style="font-size: 1rem; font-weight: 600; color: #333;">{{ $item['name'] ?? 'Unknown Product' }}</h6>
                                                        <div class="d-flex flex-wrap gap-3 mb-2">
                                                            <p class="text-muted small mb-0">Size: <strong style="color: #490D59;">{{ $item['size'] ?? 'N/A' }}</strong></p>
                                                            <p class="text-muted small mb-0">Quantity: <strong style="color: #490D59;">{{ $item['quantity'] ?? 1 }}</strong></p>
                                                        </div>
                                                        <p class="mb-0">
                                                            <strong style="color: #490D59; font-size: 1.1rem;">₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) }}</strong>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 flex-wrap pt-3 border-top">
                                    <a href="{{ route('frontend.parent.track-order', ['orderId' => $order['id']]) }}" class="vs-btn btn-sm" style="background-color: #dc3545; color: #ffffff; border: none; padding: 10px 20px;">
                                        <i class="fas fa-truck me-2"></i> Track Order
                                    </a>
                                    <a href="{{ route('frontend.parent.return-exchange', ['orderId' => $order['id']]) }}" class="vs-btn btn-sm" style="background: #6c757d; color: #ffffff; border: none; padding: 10px 20px;">
                                        <i class="fas fa-exchange-alt me-2"></i> Return/Exchange
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">No orders yet</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn">Go to Dashboard</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection


