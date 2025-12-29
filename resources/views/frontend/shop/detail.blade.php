@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper " data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">{{ $product['name'] ?? 'Product Details' }}</h1>
            <p class="breadcumb-text">{{ Str::limit($product['description'] ?? 'Explore Product Details', 80) }}</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><a href="{{ route('frontend.shop.index') }}">Shop</a></li>
                    <li>{{ Str::limit($product['name'] ?? 'Product Details', 30) }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="vs-product-wrapper product-details space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row gx-60">
            <!-- Left: Product Images -->
            <div class="col-lg-6">
                <div class="product-big-img vs-carousel" data-slide-show="1" data-fade="true" data-asnavfor=".product-thumb-slide">
                        @php
                            $productImages = $product['images'] ?? [$product['image'] ?? asset('assets/img/no image/no_image.png')];
                        @endphp
                        @foreach($productImages as $index => $image)
                        <div class="img">
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                            </div>
                        @endforeach
                    </div>
                <div class="product-thumb-slide row vs-carousel" data-slide-show="4" data-md-slide-show="4" data-sm-slide-show="3" data-xs-slide-show="3" data-asnavfor=".product-big-img">
                    @foreach($productImages as $index => $image)
                        <div class="col-3">
                            <div class="thumb">
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                        </div>
                            </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class="col-lg-6 align-self-center">
                <div class="product-about">
                    <p class="product-price">
                        ₹{{ number_format($product['price']) }}
                        @if(isset($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] != $product['price'])
                            <del>₹{{ number_format($product['original_price']) }}</del>
                        @endif
                    </p>
                    
                    <h2 class="product-title">{{ $product['name'] }}</h2>
                    
                    <p class="product-text">{{ $product['description'] ?? 'Unknown Description' }}</p>

                    @if(!empty($product['delivery_duration']))
                    <div class="delivery-info" style="margin-bottom: 20px; padding: 12px 16px; background-color: #f7f2fb; border-left: 4px solid #490D59; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-truck" style="color: #490D59; font-size: 16px;"></i>
                            <span style="color: #374151; font-size: 14px; font-weight: 500;">
                                <strong>Delivery:</strong> {{ $product['delivery_duration'] }}
                            </span>
                        </div>
                    </div>
                    @endif

                    @php
                        $defaultSize = isset($product['sizes']) && count($product['sizes']) > 0 ? $product['sizes'][0] : 'Standard';
                    @endphp
                    <form action="{{ route('frontend.shop.add-to-cart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="size" id="cart-size" value="{{ $defaultSize }}" required>
                        <input type="hidden" name="quantity" id="cart-quantity" value="1" required>
                        
                        <!-- Size Selection -->
                        @if(isset($product['sizes']) && count($product['sizes']) > 0)
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="form-label fw-bold mb-0">Size:</label>
                                @if(!empty($product['size_chart_path']) || !empty($product['size_measurement_image']) || !empty($product['video_url']))
                                    <a href="#" class="text-primary small" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" style="text-decoration: underline;">Size Guide</a>
                                @endif
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product['sizes'] as $size)
                                    @php
                                        $isOutOfStock = false;
                                        if(isset($product['variants']) && count($product['variants']) > 0) {
                                            $variant = $product['variants']->firstWhere('option', $size);
                                            if($variant) {
                                                $isOutOfStock = $variant->stock <= 0;
                                            }
                                        }
                                        // Auto-select first available size
                                        $checked = false;
                                        if (!$isOutOfStock && !isset($hasChecked)) {
                                            $checked = true;
                                            $hasChecked = true;
                                        }
                                    @endphp
                                    <label class="size-option {{ $isOutOfStock ? 'disabled' : '' }}" {{ $isOutOfStock ? 'title=Out-of-stock' : '' }}>
                                        <input type="radio" name="size" value="{{ $size }}" {{ $checked ? 'checked' : '' }} {{ $isOutOfStock ? 'disabled' : '' }} required>
                                        <span>{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </form>

                        <!-- Actions -->
                        <div class="actions">
                            <div class="quantity">
                                <label for="quantity" class="screen-reader-text">Quantity:</label>
                                <button type="button" class="quantity-minus qty-btn"><i class="fal fa-minus"></i></button>
                                <input type="number" id="quantity" class="qty-input" step="1" min="1" max="100" name="quantity" value="1" title="Qty" form="addToCartForm">
                                <button type="button" class="quantity-plus qty-btn"><i class="fal fa-plus"></i></button>
                            </div>
                            <button type="submit" class="vs-btn" form="addToCartForm">Add to Cart</button>
                        </div>

                    <div class="product-getway" style="margin-top: 22px !important;">
                        <span class="getway-title">GUARANTEED SAFE CHECKOUT:</span>
                        <img src="{{ asset('assets/img/widget/cards-2.png') }}" alt="cards">
                    </div>
                
                <div class="product_meta">
                        @if(isset($product['tags']) && count($product['tags']) > 0)
                            <span>Tags: 
                                @foreach($product['tags'] as $tag)
                                    <a href="#" rel="tag">{{ $tag }}</a>
                                @endforeach
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        <!-- Related Products Section -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h2>Related Products</h2>
                <div class="title-divider1"></div>
                <div class="row vs-carousel" data-slide-show="4" data-lg-slide-show="3" data-md-slide-show="2" data-sm-slide-show="2" data-xs-slide-show="2">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-6 col-lg-3 col-xl-3">
                            <div class="vs-product product-style1">
                                <div class="product-img">
                                    <a href="{{ route('frontend.shop.detail', $relatedProduct['id']) }}">
                                        @if(isset($relatedProduct['image']) && $relatedProduct['image'])
                                            <img src="{{ $relatedProduct['image'] }}" alt="{{ $relatedProduct['name'] }}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                        @else
                                            <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $relatedProduct['name'] }}" class="w-100">
                                        @endif
                                    </a>
                            </div>
                                <div class="product-content">
                                    <span class="product-price">
                                        ₹{{ number_format($relatedProduct['price'] ?? 0) }}
                                        @if(isset($relatedProduct['original_price']) && $relatedProduct['original_price'] > ($relatedProduct['price'] ?? 0))
                                            <del>₹{{ number_format($relatedProduct['original_price']) }}</del>
                                        @endif
                                    </span>
                                    <h3 class="product-title">
                                        <a class="text-inherit" href="{{ route('frontend.shop.detail', $relatedProduct['id']) }}">
                                            {{ $relatedProduct['name'] }}
                                        </a>
                                    </h3>
                                    <div class="actions">
                                        <a href="{{ route('frontend.shop.detail', $relatedProduct['id']) }}" class="vs-btn">
                                            <i class="far fa-shopping-cart"></i>View Details
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Login Required Modal -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 20px 24px;">
                <h5 class="modal-title" id="loginRequiredModalLabel" style="color: #111827; font-weight: 600; font-size: 20px;">
                    <i class="fas fa-lock" style="color: #490D59; margin-right: 10px;"></i>
                    Login Required
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div class="text-center mb-4">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f7f2fb 0%, #ede7f3 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-shopping-cart" style="font-size: 36px; color: #490D59;"></i>
                    </div>
                    <h4 style="color: #111827; font-weight: 600; margin-bottom: 16px;">Please Login to Continue</h4>
                    <div style="background: #f9fafb; border-left: 4px solid #490D59; padding: 16px; border-radius: 8px; text-align: left; margin-bottom: 20px;">
                        <p style="color: #374151; font-size: 14px; line-height: 1.7; margin: 0 0 12px 0; font-weight: 500;">
                            <i class="fas fa-info-circle" style="color: #490D59; margin-right: 8px;"></i>
                            <strong>Choose your shopping mode:</strong>
                        </p>
                        <ul style="color: #6b7280; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 24px;">
                            <li style="margin-bottom: 8px;">If you want to <strong>buy this product for yourself</strong>, you can continue as <strong>Guest</strong> after logging in.</li>
                            <li style="margin-bottom: 0;">If you want to <strong>purchase this product for your child</strong>, continue as <strong>Parent</strong> after logging in.</li>
                        </ul>
                    </div>
                    <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0;">
                        Please login to proceed with your purchase.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px; display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px; font-weight: 500; border: 1px solid #d1d5db; background: #fff; color: #374151;">
                    Cancel
                </button>
                <a href="{{ route('login') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #490D59 0%, #6b1179 100%); border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600; text-decoration: none; color: white; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Size Guide Modal -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeGuideModalLabel">Size Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                     @if(!empty($product['size_chart_path']))
                        <div class="{{ (!empty($product['size_measurement_image']) || !empty($product['video_url'])) ? 'col-md-6' : 'col-12' }}">
                            <div class="size-guide-image">
                                <img src="{{ asset('storage/' . $product['size_chart_path']) }}" alt="Size Guide" class="w-100" style="border-radius: 8px; object-fit: contain;">
                            </div>
                        </div>
                    @endif
                    
                    @if(!empty($product['size_measurement_image']))
                        <div class="{{ (!empty($product['size_chart_path']) || !empty($product['video_url'])) ? 'col-md-6' : 'col-12' }}">
                            <div class="size-guide-image">
                                <img src="{{ asset('storage/' . $product['size_measurement_image']) }}" alt="Size Measurement" class="w-100" style="border-radius: 8px; object-fit: contain;">
                            </div>
                        </div>
                    @endif
                    
                    @if(!empty($product['video_url']))
                        <div class="{{ (!empty($product['size_chart_path']) || !empty($product['size_measurement_image'])) ? 'col-md-6' : 'col-12' }}">
                            <div class="size-guide-video">
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ str_replace('watch?v=', 'embed/', $product['video_url']) }}" title="Size Guide Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Copied Styles from Original */
    
    .product-big-img { margin-bottom: 15px; }
    .product-big-img .img { width: 100%; height: 500px; background-color: #f8f5ff; border-radius: 12px; overflow: hidden; margin: 0; border: 2px solid #e0d5f0; }
    .product-big-img .img img { width: 100%; height: 100%; object-fit: contain; display: block; }

    .product-thumb-slide { margin: 0 -5px; }
    .product-thumb-slide .col-3 { padding: 0 5px; }
    .product-thumb-slide .thumb { width: 100%; height: 100px; border: 2px solid #e0d5f0; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s ease; background-color: #f8f5ff; display: flex; align-items: center; justify-content: center; }
    .product-thumb-slide .thumb img { width: 100%; height: 100%; object-fit: contain; }
    .product-thumb-slide .thumb:hover, .product-thumb-slide .thumb.active { border-color: #490D59; }

    /* Size Guide Modal Images */
    .size-guide-image {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border-radius: 8px;
        padding: 10px;
        min-height: 300px;
    }
    
    .size-guide-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }

    .title-divider1 { height: 3px; width: 80px; background-color: #490D59; margin-bottom: 20px; }
    
    .vs-product.product-style1 { display: flex; flex-direction: column; height: 100%; border: 3px solid #e0d5f0; border-radius: 30px; transition: all 0.4s; overflow: hidden; }
    .vs-product.product-style1:hover { border-color: #490D59; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
    .product-img { width: 100%; height: 280px; display: flex; align-items: center; justify-content: center; background-color: #fff; }
    .product-img img { width: 100%; height: 100%; object-fit: contain; padding: 15px; }
    
    .product-price { font-size: 22px; font-weight: 500; color: #dc3545; }
    .product-title { font-size: 16px; margin-bottom: 12px; text-transform: capitalize; }
    
    .size-option { position: relative; display: inline-block; }
    .size-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; }
    .size-option span { display: inline-block; padding: 10px 20px; border: 2px solid #e0d5f0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; background-color: white; min-width: 60px; text-align: center; }
    .size-option input[type="radio"]:checked + span { border-color: #490D59; background-color: #490D59; color: white; }
    .size-option.disabled span { background-color: #f2f4f7; color: #98a2b3; border-color: #eaecf0; cursor: not-allowed; text-decoration: line-through; }

    .product-about { background-color: #fff; padding: 30px; border-radius: 12px; }
    .quantity { display: flex; align-items: center; border: 1px solid #fff; }
    .qty-btn { width: 40px; height: 50px; background: #fff; border: none; cursor: pointer; }
    .qty-btn:hover { background: #490D59; color: #fff; }
    .qty-input { width: 60px; height: 50px; border: none; text-align: center; font-size: 16px; font-weight: 600; }
    .actions { display: flex; align-items: center; gap: 10px; margin-bottom: 2px !important; }
    .product-style1 .actions { margin-bottom: 2px !important; }

    /* Login Modal Scrollable Styles */
    #loginRequiredModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: #490D59 #f0f0f0;
    }
    
    #loginRequiredModal .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    #loginRequiredModal .modal-body::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }
    
    #loginRequiredModal .modal-body::-webkit-scrollbar-thumb {
        background: #490D59;
        border-radius: 4px;
    }
    
    #loginRequiredModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #6b1179;
    }

    @media (max-width: 767px) {
        #loginRequiredModal .modal-body {
            max-height: 60vh;
        }
        .product-big-img .img { height: 300px; }
        .product-about { padding: 20px; margin-top: 20px; }
        .actions { flex-wrap: wrap; }
        .product-style1 .vs-btn { min-width: 100%; order: 2; }
        .quantity { width: 100%; justify-content: center; margin-bottom: 10px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const quantityMinus = document.querySelector('.quantity-minus');
        const quantityPlus = document.querySelector('.quantity-plus');
        const cartQuantity = document.getElementById('cart-quantity');
        const cartSizeInput = document.getElementById('cart-size');
        const sizeInputs = document.querySelectorAll('input[name="size"]');
        const addToCartForm = document.getElementById('addToCartForm');

        // Quantity +/- buttons - Remove jQuery handlers and attach our own
        // Use setTimeout to ensure this runs after main.js jQuery handlers are attached
        setTimeout(function() {
            const qtyMinus = document.querySelector('.quantity-minus');
            const qtyPlus = document.querySelector('.quantity-plus');
            const qtyInput = document.getElementById('quantity');
            const cartQty = document.getElementById('cart-quantity');
            
            if (qtyMinus && qtyInput) {
                // Remove any existing jQuery handlers
                if (typeof jQuery !== 'undefined') {
                    jQuery(qtyMinus).off('click');
                }
                // Remove any existing event listeners by cloning and replacing
                const newMinus = qtyMinus.cloneNode(true);
                qtyMinus.parentNode.replaceChild(newMinus, qtyMinus);
                
                newMinus.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    let currentValue = parseInt(qtyInput.value) || 1;
                    const minValue = parseInt(qtyInput.getAttribute('min')) || 1;
                    if (currentValue > minValue) {
                        qtyInput.value = currentValue - 1;
                        if (cartQty) cartQty.value = qtyInput.value;
                    }
                    return false;
                }, true);
            }

            if (qtyPlus && qtyInput) {
                // Remove any existing jQuery handlers
                if (typeof jQuery !== 'undefined') {
                    jQuery(qtyPlus).off('click');
                }
                // Remove any existing event listeners by cloning and replacing
                const newPlus = qtyPlus.cloneNode(true);
                qtyPlus.parentNode.replaceChild(newPlus, qtyPlus);
                
                newPlus.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    let currentValue = parseInt(qtyInput.value) || 1;
                    const maxValue = parseInt(qtyInput.getAttribute('max')) || 100;
                    if (currentValue < maxValue) {
                        qtyInput.value = currentValue + 1;
                        if (cartQty) cartQty.value = qtyInput.value;
                    }
                    return false;
                }, true);
            }
        }, 100);
        
        // Update cart quantity when input changes
        if (quantityInput && cartQuantity) {
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 1;
                const min = parseInt(this.getAttribute('min')) || 1;
                const max = parseInt(this.getAttribute('max')) || 100;
                
                if (value < min) value = min;
                if (value > max) value = max;
                
                this.value = value;
                cartQuantity.value = value;
            });
        }

        // Update cart size when size is selected
        if (sizeInputs.length > 0) {
            sizeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked && cartSizeInput) {
                        cartSizeInput.value = this.value;
                    }
                });
            });
        } 

        // Form submission validation with authentication check
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                // Check if user is authenticated
                const isAuthenticated = @json(auth()->check());
                
                if (!isAuthenticated) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Show login modal
                    const loginModalElement = document.getElementById('loginRequiredModal');
                    if (loginModalElement) {
                        // Check if Bootstrap is available
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            const loginModal = new bootstrap.Modal(loginModalElement);
                            loginModal.show();
                        } else if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                            // Fallback to jQuery Bootstrap modal
                            jQuery(loginModalElement).modal('show');
                        } else {
                            // Fallback: redirect to login
                            window.location.href = '{{ route("login") }}';
                        }
                    } else {
                        // If modal doesn't exist, redirect to login
                        window.location.href = '{{ route("login") }}';
                    }
                    return false;
                }
                
                // Update cart quantity before submission
                if (cartQuantity && quantityInput) {
                    cartQuantity.value = quantityInput.value;
                }
            });
        }
    });
</script>

@endsection
