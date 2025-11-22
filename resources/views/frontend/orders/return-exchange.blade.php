@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h3 mb-2">Return/Exchange</h2>
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
                        <h5 class="mb-4">Request Return/Exchange</h5>
                        
                        <form action="{{ route('frontend.parent.request-return-exchange') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order['id'] }}">

                            <!-- Select Reason -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Reason</label>
                                <select class="form-select" name="reason" required>
                                    <option value="">Choose a reason</option>
                                    <option value="WRONG SIZE">Wrong Size</option>
                                    <option value="WRONG ITEM">Wrong Item</option>
                                    <option value="DAMAGED PRODUCT">Damaged Product</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>

                            <!-- Upload Supporting Photo -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload Supporting Photo</label>
                                <input type="file" class="form-control" name="photo" accept="image/*">
                                <small class="text-muted">Upload a photo to support your return/exchange request</small>
                            </div>

                            <!-- Choose Action -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Choose Action</label>
                                <div class="d-flex gap-3">
                                    <label class="action-option">
                                        <input type="radio" name="action" value="return" required>
                                        <span>Return</span>
                                    </label>
                                    <label class="action-option">
                                        <input type="radio" name="action" value="exchange" required>
                                        <span>Exchange</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Return Policy -->
                            <div class="mb-4">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><strong>Return Policy</strong></h6>
                                    <ul class="mb-0 small">
                                        <li>Items must be returned within 7 days of delivery</li>
                                        <li>Products must be in original condition with tags</li>
                                        <li>Refund will be processed within 5-7 business days</li>
                                        <li>Exchange items will be dispatched after verification</li>
                                    </ul>
                                </div>
                            </div>

                            <button type="submit" class="vs-btn">
                                <i class="fas fa-paper-plane me-2"></i> Submit Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body">
                        <h5 class="mb-3">Order Items</h5>
                        @foreach($order['items'] as $item)
                            <div class="d-flex gap-2 mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 small">{{ $item['name'] }}</h6>
                                    <p class="text-muted small mb-0">Size: {{ $item['size'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .action-option {
        position: relative;
        display: inline-block;
    }

    .action-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .action-option span {
        display: inline-block;
        padding: 10px 30px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .action-option input[type="radio"]:checked + span {
        border-color: #490D59;
        background-color: #490D59;
        color: #ffffff;
    }

    .action-option:hover span {
        border-color: #490D59;
    }
</style>
@endsection

