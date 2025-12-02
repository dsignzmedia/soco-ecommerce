@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h3 mb-2">Track Order</h2>
                        <p class="text-muted mb-0">Order #{{ $order['id'] }}</p>
                    </div>
                    <a href="{{ route('frontend.parent.orders') }}" class="vs-btn btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Order Status</h5>
                        
                        <!-- Status Timeline -->
                        <div class="order-timeline">
                            @foreach($statuses as $index => $status)
                                <div class="timeline-item {{ $index <= $currentStatusIndex ? 'completed' : '' }} {{ $index == $currentStatusIndex ? 'current' : '' }}">
                                    <div class="timeline-marker">
                                        @if($index <= $currentStatusIndex)
                                            <i class="fas fa-check-circle"></i>
                                        @else
                                            <i class="far fa-circle"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-{{ $status['icon'] }}" style="font-size: 1.1rem;"></i>
                                            <h6 class="mb-0">{{ $status['label'] }}</h6>
                                        </div>
                                        @if($index < $currentStatusIndex)
                                            <p class="text-muted small mb-0">✅ Completed</p>
                                        @elseif($index == $currentStatusIndex)
                                            <p class="text-primary small mb-0 fw-bold">🔵 Current Status</p>
                                        @else
                                            <p class="text-muted small mb-0">⏳ Pending</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body">
                        <h5 class="mb-3">Order Details</h5>
                        <p class="mb-2"><strong>Order ID:</strong> {{ $order['id'] }}</p>
                        <p class="mb-2"><strong>Status:</strong> {{ $order['status'] }}</p>
                        <p class="mb-2"><strong>Total:</strong> ₹{{ number_format($order['total']) }}</p>
                        <p class="mb-0"><strong>Date:</strong> {{ date('M d, Y', strtotime($order['created_at'])) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .order-timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        border: 2px solid #e0d5f0;
        border-radius: 50%;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #28a745;
        border-color: #28a745;
        color: #ffffff;
    }
    
    .timeline-item.current .timeline-marker {
        background-color: #490D59;
        border-color: #490D59;
        color: #ffffff;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: -20px;
        top: 30px;
        width: 2px;
        height: calc(100% - 10px);
        background-color: #e0d5f0;
        z-index: 1;
    }

    .timeline-item.completed:not(:last-child)::after {
        background-color: #28a745;
    }

    .timeline-content h6 {
        color: #333;
        font-weight: 600;
    }

    .timeline-item.completed .timeline-content h6 {
        color: #28a745;
    }
    
    .timeline-item.current .timeline-content h6 {
        color: #490D59;
        font-weight: 700;
    }
</style>
@endsection

