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
                    <a href="{{ route('frontend.parent.orders') }}" class="vs-btn btn-sm d-none d-md-inline-flex">
                        <i class="fas fa-arrow-left me-2"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('frontend.parent.request-return-exchange') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Request Return/Exchange</h5>
                            
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
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body">
                            <h5 class="mb-3">Select Items to Return/Exchange</h5>
                            <p class="text-muted small mb-3">Please select the items you wish to return or exchange.</p>
                            
                            @if($errors->has('selected_items'))
                                <div class="alert alert-danger small py-2 mb-3">
                                    {{ $errors->first('selected_items') }}
                                </div>
                            @endif

                            @foreach($order['items'] as $item)
                                <label class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom position-relative w-100" style="cursor: pointer;">
                                    <div class="flex-shrink-0">
                                        <input class="form-check-input border-secondary" type="checkbox" name="selected_items[]" value="{{ $item['id'] }}" @checked(empty($selectedItems) || in_array($item['id'], $selectedItems ?? [])) style="width: 1.3em; height: 1.3em; cursor: pointer;">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                                            @if($item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid rounded" style="max-height: 100%; max-width: 100%;">
                                            @else
                                                <i class="fas fa-image text-muted"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small text-dark fw-bold">{{ $item['name'] }}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Size: {{ $item['size'] }}</span>
                                            <span class="text-primary small fw-bold">₹{{ number_format($item['price'], 2) }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

