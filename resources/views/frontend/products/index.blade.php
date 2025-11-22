@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        @if($selectedProfile)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="mb-1">Shopping for: <strong>{{ $selectedProfile['student_name'] }}</strong></h5>
                                    <p class="text-muted mb-0">{{ $selectedProfile['school_name'] }} - Grade {{ $selectedProfile['grade'] }}, Section {{ $selectedProfile['section'] }}</p>
                                </div>
                                <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn btn-sm">Change Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 mb-3">Products</h2>
                <p class="text-muted">Select products for your student</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff;">
                        <div class="card-img-top position-relative" style="height: 250px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">{{ ucfirst($product['type']) }}</span>
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <p class="text-muted mb-3">Product description goes here...</p>
                            <div class="mb-3">
                                <label class="form-label small">Select Size</label>
                                <select class="form-select form-select-sm" id="size_{{ $product['id'] }}">
                                    <option value="">Choose Size</option>
                                    @foreach($product['sizes'] as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="h5 mb-0">₹{{ $product['price'] }}</span>
                                </div>
                                <div>
                                    <small class="text-muted">Delivery: 3-5 days</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="vs-btn flex-fill add-to-cart-btn" data-product-id="{{ $product['id'] }}" data-profile-id="{{ $selectedProfile['id'] ?? '' }}">
                                    <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                </button>
                                @if(isset($selectedProfile) && $selectedProfile)
                                    <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="vs-btn flex-fill" style="background-color: #dc3545; border: none;">
                                        <i class="fas fa-bolt me-2"></i> Buy Now
                                    </a>
                                @else
                                    <a href="{{ route('frontend.parent.store') }}" class="vs-btn flex-fill" style="background-color: #dc3545; border: none;">
                                        <i class="fas fa-bolt me-2"></i> Buy Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5>No products available</h5>
                            <p class="text-muted">Products will be displayed here based on your student's profile.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const profileId = this.dataset.profileId;
            const sizeSelect = document.getElementById('size_' + productId);
            const selectedSize = sizeSelect.value;

            if (!selectedSize) {
                alert('Please select a size');
                return;
            }

            if (!profileId) {
                alert('Please select a student profile first');
                window.location.href = '{{ route("frontend.parent.dashboard") }}';
                return;
            }

            // In production, this would add to cart via AJAX
            alert('Product added to cart!');
            // Redirect to cart page
            // window.location.href = '{{ route("frontend.parent.cart") }}';
        });
    });
});
</script>
@endsection

