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
            <p class="breadcumb-text">{{ Str::limit($product['description'] ?? 'Explore Product Details, Reviews, And Specifications', 80) }}</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><a href="{{ route('frontend.parent.dashboard', ['student_id' => $selectedProfile['id'] ?? '']) }}">Parent Dashboard</a></li>
                    <li><a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id'] ?? '']) }}">Store</a></li>
                    <li>{{ Str::limit($product['name'] ?? 'Product Details', 30) }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="vs-product-wrapper product-details space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row gx-60">
            <!-- Left: Product Images & Videos -->
            <div class="col-lg-6">
                <div class="product-big-img vs-carousel" data-slide-show="1" data-fade="true" data-asnavfor=".product-thumb-slide">
                        @php
                            $productImages = $product['images'] ?? [$product['image'] ?? asset('assets/img/no image/no_image.png')];
                        @endphp
                        @foreach($productImages as $index => $image)
                        @php
                            $isVideo = preg_match('/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv|m3u8)(\?.*)?$/i', $image);
                            $mediaUrl = \Illuminate\Support\Str::startsWith($image, 'http') ? $image : asset('storage/' . $image);
                        @endphp
                        <div class="img">
                                @if($isVideo)
                                    <video controls style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
                                        <source src="{{ $mediaUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                @endif
                            </div>
                        @endforeach
                    </div>
                <div class="product-thumb-slide row vs-carousel" data-slide-show="4" data-md-slide-show="4" data-sm-slide-show="3" data-xs-slide-show="3" data-asnavfor=".product-big-img">
                    @foreach($productImages as $index => $image)
                        @php
                            $isVideo = preg_match('/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv|m3u8)(\?.*)?$/i', $image);
                            $mediaUrl = \Illuminate\Support\Str::startsWith($image, 'http') ? $image : asset('storage/' . $image);
                        @endphp
                        <div class="col-3">
                            <div class="thumb">
                                @if($isVideo)
                                    <div style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #000;">
                                        <video style="width: 100%; height: 100%; object-fit: contain;" preload="metadata">
                                            <source src="{{ $mediaUrl }}" type="video/mp4">
                                        </video>
                                        <i class="fas fa-play" style="position: absolute; color: white; font-size: 20px; z-index: 1;"></i>
                                    </div>
                                @else
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                @endif
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
                        @if(isset($product['original_price']) && $product['original_price'] > $product['price'])
                            <del>₹{{ number_format($product['original_price']) }}</del>
                        @endif
                    </p>
                    
                    <h2 class="product-title">{{ $product['name'] }}</h2>
                    
                    {{-- <div class="product-rating">
                        <div class="star-rating" role="img" aria-label="Rated {{ number_format($averageRating, 2) }} out of 5">
                            <span style="width:{{ ($averageRating / 5) * 100 }}%">Rated <strong class="rating">{{ number_format($averageRating, 2) }}</strong> out of 5 based on <span class="rating">{{ $reviewCount }}</span> customer rating</span>
                        </div>
                        <span>({{ $reviewCount }} reviews)</span>
                    </div> --}}

                    <p class="product-text">{{ $product['description'] ?? 'Premium quality product with excellent craftsmanship and attention to detail. We think your skin should look and refreshed matter Nourish your outer inner beauty with our essential oil infused beauty products.' }}</p>

                    @php
                        $defaultSize = isset($product['sizes']) && count($product['sizes']) > 0 ? $product['sizes'][0] : 'Standard';
                    @endphp
                    <form action="{{ route('frontend.parent.add-to-cart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
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

                    
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @php
            $relatedProductsList = $relatedProducts ?? ($allProducts ?? []);
            $relatedProductsList = array_slice($relatedProductsList, 0, 4);
        @endphp

        @if(count($relatedProductsList) > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h2>Related Products</h2>
                <div class="title-divider1"></div>
                <div class="row vs-carousel" data-slide-show="4" data-lg-slide-show="3" data-md-slide-show="2" data-sm-slide-show="2" data-xs-slide-show="2">
                    @foreach($relatedProductsList as $relatedProduct)
                        <div class="col-md-6 col-lg-3 col-xl-3">
                            <div class="vs-product product-style1">
                                <div class="product-img">
                                    <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}">
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
                                        <a class="text-inherit" href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}">
                                            {{ $relatedProduct['name'] }}
                                        </a>
                                    </h3>
                                    <div class="actions">
                                        <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}" class="vs-btn">
                                            <i class="far fa-shopping-cart"></i>Add to Cart
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
                    @php
                            $videoUrl = $product['video_url'];
                            $videoId = '';
                            if (Str::contains($videoUrl, 'youtu.be/')) {
                                $videoId = explode('youtu.be/', $videoUrl)[1];
                            } elseif (Str::contains($videoUrl, 'watch?v=')) {
                                parse_str(parse_url($videoUrl, PHP_URL_QUERY), $query);
                                $videoId = $query['v'] ?? '';
                            } elseif (Str::contains($videoUrl, 'shorts/')) {
                                $videoId = explode('shorts/', $videoUrl)[1];
                            }
                            $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : '';
                        @endphp
                        @if(!empty($embedUrl))
                            <div class="{{ (!empty($product['size_chart_path']) || !empty($product['size_measurement_image'])) ? 'col-md-6' : 'col-12' }}">
                                <div class="size-guide-video">
                                    <div class="ratio ratio-16x9">
                                        <iframe
                                            src="{{ $embedUrl }}"
                                            title="Size Guide Video"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Product Image Carousel */
    .product-big-img {
        margin-bottom: 15px;
    }

    .product-big-img .img {
        width: 100%;
        height: 500px;
        background-color: #f8f5ff;
        border-radius: 12px;
        overflow: hidden;
        padding: 0;
        margin: 0;
        border: 2px solid #e0d5f0;
    }

    .product-big-img .img img,
    .product-big-img .img video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        border-radius: 12px;
    }
    
    .product-big-img .img video {
        background: #000;
    }

    .product-thumb-slide {
        margin: 0 -5px;
    }

    .product-thumb-slide .col-3 {
        padding: 0 5px;
    }

    .product-thumb-slide .thumb {
        width: 100%;
        height: 100px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f5ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-thumb-slide .thumb img,
    .product-thumb-slide .thumb video {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .product-thumb-slide .thumb video {
        background: #000;
    }

    .product-thumb-slide .thumb:hover,
    .product-thumb-slide .thumb.active {
        border-color: #490D59;
    }

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

    .title-divider1 {
        height: 3px;
        width: 80px;
        background-color: #490D59;
        margin-bottom: 20px;
    }

    /* Related Products Styles */
    .product-img-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 30px 30px 0 0;
        background-color: #ffffff;
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #000000;
        color: #ffffff;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        z-index: 2;
        border-radius: 4px;
    }

    .product-img {
        width: 100%;
        height: 280px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
    }

    .vs-product.product-style1 {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 3px solid var(--theme-color2, #e0d5f0);
        border-radius: 30px;
        transition: border-color ease 0.4s, box-shadow ease 0.4s;
        overflow: hidden;
        position: relative;
    }

    .vs-product.product-style1:hover {
        border-color: var(--theme-color, #490D59);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .product-content {
        padding: 8px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-price {
        font-size: 22px;
        font-weight: 500;
        color: #dc3545;
        font-family: var(--title-font, inherit);
        margin-bottom: 12px;
        display: block;
        line-height: 1;
    }

    .product-title {
        font-size: 16px;
        margin-bottom: 12px;
        text-transform: capitalize;
        line-height: 1.4;
        min-height: 44px;
    }

    .product-title a {
        color: #333;
        text-decoration: none;
    }

    .product-title a:hover {
        color: #490D59;
    }

    .actions {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-style1 .vs-btn {
        flex: 1;
    }

    .product-style1 .vs-btn i {
        margin-right: 8px;
        font-size: 14px;
    }


    .size-option {
        position: relative;
        display: inline-block;
    }

    .size-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .size-option span {
        display: inline-block;
        padding: 10px 20px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
        min-width: 60px;
        text-align: center;
    }

    .size-option input[type="radio"]:checked + span {
        border-color: #490D59;
        background-color: #490D59;
        color: #ffffff;
    }

    .size-option:hover span {
        border-color: #490D59;
    }

    .size-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .size-option.disabled span {
        background-color: #f2f4f7;
        color: #98a2b3;
        border-color: #eaecf0;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    
    .size-option.disabled:hover span {
        border-color: #eaecf0;
    }

    .form-select {
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 16px;
        font-weight: 600;
    }

    .form-select:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
        outline: none;
    }

    .product-about {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: none;
        border: none;
        outline: none;
    }

    .product-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 32px;
        font-weight: 600;
        color: #dc3545;
        margin-bottom: 15px;
    }

    .product-price del {
        font-size: 24px;
        color: #999;
        margin-left: 10px;
        font-weight: 400;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .product-rating .star-rating {
        margin-bottom: 0;
        font-size: 14px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        overflow: hidden;
    }

    .product-rating .star-rating span {
        display: block;
        position: relative;
        height: 1em;
        line-height: 1;
        font-size: 1em;
        width: 5.4em;
        font-family: star;
        color: #ffb900;
    }

    .product-rating .star-rating span:before {
        content: "\f005\f005\f005\f005\f005";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #ffb900;
        font-size: 14px;
        letter-spacing: 2px;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .product-rating .star-rating span strong,
    .product-rating .star-rating span {
        font-size: 0;
        line-height: 0;
        color: transparent;
        overflow: hidden;
    }

    .product-rating .star-rating .rating {
        display: none;
    }

    .product-rating > span {
        color: #666;
        font-size: 14px;
        display: inline-block;
        margin-left: 5px;
    }

    .product-text {
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    /* Quantity Buttons */
    .quantity {
        display: flex;
        align-items: center;
        border: 1px solid #ffffff;
        border-radius: 8px;
        overflow: hidden;
        background-color: #ffffff;
    }

    .qty-btn {
        width: 40px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
    }

    .qty-btn:hover {
        background-color: #490D59;
        color: #ffffff;
    }

    .qty-input {
        width: 60px;
        height: 50px;
        border: none;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        padding: 0;
        background-color: #ffffff;
    }

    .qty-input:focus {
        outline: none;
        border: none;
        box-shadow: none;
    }
    
    .quantity:focus,
    .quantity:focus-within {
        outline: none;
        border: 1px solid #ffffff;
        box-shadow: none;
    }
    
    .product-about:focus,
    .product-about:focus-within {
        outline: none;
        border: none;
        box-shadow: none;
    }

    .screen-reader-text {
        position: absolute;
        clip: rect(1px, 1px, 1px, 1px);
        width: 1px;
        height: 1px;
        overflow: hidden;
    }

    .actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 2px !important;
    }
    
    .product-style1 .actions {
        margin-bottom: 2px !important;
    }

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

    /* Product Getway */
    .product-getway {
        margin-top: 22px !important;
        padding-top: 25px;
    }

    .getway-title {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-getway img {
        max-width: 100%;
        height: auto;
    }

    /* Product Meta */
    .product_meta {
        display: flex;
            flex-direction: column;
        gap: 10px;
        font-size: 14px;
        color: #666;
        padding-top: 20px;
    }

    .product_meta span {
        display: block;
    }

    .product_meta a {
        color: #490D59;
        text-decoration: none;
        margin-left: 5px;
    }

    .product_meta a:hover {
        text-decoration: underline;
    }

    /* Mobile Responsiveness */
    @media (max-width: 767px) {
        .product-big-img .img {
            height: 300px;
        }

        .product-about {
            padding: 20px;
            margin-top: 20px;
        }

        .product-title {
            font-size: 22px;
        }

        .product-price {
            font-size: 24px;
        }

        .product-price del {
            font-size: 18px;
        }

        .actions {
            flex-wrap: wrap;
        }

        .product-style1 .vs-btn {
            min-width: 100%; /* Full width button on mobile */
            order: 2; /* Move below quantity if needed, or keep as is */
        }
        
        .quantity {
            width: 100%;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .qty-input {
            width: 80px; /* Wider input for easier tapping */
        }
        
        #loginRequiredModal .modal-body {
            max-height: 60vh;
        }

        /* Compact Breadcrumb for Mobile */
        .breadcumb-title,
        .breadcumb-text {
            display: none;
        }

        .breadcumb-wrapper {
            padding-top: 50px; /* Clear header */
            padding-bottom: 20px;
            min-height: auto;
        }

        .breadcumb-content {
            text-align: left;
        }

        .breadcumb-menu {
            justify-content: flex-start;
            margin-bottom: 0;
        }

        /* 2-Column Related Products */
        .vs-carousel .col-md-6 {
            padding: 0 5px; /* Reduce gap */
        }
        
        .vs-product.product-style1 {
            border-radius: 20px;
        }

        .product-img {
            height: 180px; /* Smaller image height */
            border-radius: 20px 20px 0 0;
        }

        .product-content {
            padding: 8px;
        }

        .product-title {
            font-size: 14px;
            min-height: 40px; /* Allow 2 lines */
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 14px;
            margin-bottom: 0 !important;
        }

        .actions .vs-btn {
            font-size: 11px;
            padding: 8px 10px;
        }
        
        .actions .icon-btn {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
    }

    @media (max-width: 991px) {
        .product-big-img .img {
            height: 400px;
            padding: 0;
        }

        .product-big-img .img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .actions {
            flex-wrap: wrap;
        }

        .actions .vs-btn {
            width: 100%;
        }
    }

    /* Reviews Section Styles */
    .woocommerce-Reviews {
        margin-bottom: 40px;
    }

    .vs-comments-wrap {
        margin-bottom: 40px;
    }

    .comment-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .vs-comment-item {
        margin-bottom: 30px;
        padding-bottom: 0;
    }

    .vs-comment-item:last-child {
        margin-bottom: 0;
    }

    .vs-post-comment {
        display: flex;
        gap: 20px;
    }

    .comment-avater {
            flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
    }

    .comment-avater img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comment-content {
        flex: 1;
    }

    .comment-content .name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .commented-on {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #999;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .commented-on i {
        font-size: 14px;
    }

    .review-rating {
        margin-bottom: 15px;
    }

    .review-rating .star-rating {
        margin-bottom: 0;
        font-size: 14px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        overflow: hidden;
    }

    .review-rating .star-rating span {
        display: block;
        position: relative;
        height: 1em;
        line-height: 1;
        font-size: 1em;
        width: 5.4em;
        font-family: star;
        color: #ffb900;
    }

    .review-rating .star-rating span:before {
        content: "\f005\f005\f005\f005\f005";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #ffb900;
        font-size: 14px;
        letter-spacing: 2px;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .review-rating .star-rating span strong,
    .review-rating .star-rating span {
        font-size: 0;
        line-height: 0;
        color: transparent;
        overflow: hidden;
    }

    .review-rating .star-rating .rating {
        display: none;
    }

    .comment-content .text {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Review Form Styles */
    .vs-comment-form {
        margin-top: 40px;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .form-title {
        margin-bottom: 25px;
    }

    .blog-inner-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }

    .rating-select {
        margin-bottom: 20px;
    }

    .rating-select label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .stars {
        margin: 0;
    }

    .stars span {
        display: flex;
        gap: 5px;
    }

    .stars a {
        color: #ddd;
        font-size: 20px;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .stars a:hover,
    .stars a.active {
        color: #ffb900;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }

    .form-control::placeholder {
        color: #999;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    @media (max-width: 767px) {
        .vs-post-comment {
            flex-direction: column;
        }

        .comment-avater {
            width: 60px;
            height: 60px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizeInputs = document.querySelectorAll('input[name="size"]');
        const cartSizeInput = document.getElementById('cart-size');
        const quantityInput = document.getElementById('quantity');
        const quantityMinus = document.querySelector('.quantity-minus');
        const quantityPlus = document.querySelector('.quantity-plus');
        const addToCartForm = document.getElementById('addToCartForm');
        const cartQuantity = document.getElementById('cart-quantity');

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
                    if (currentValue > 1) {
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
            if (cartSizeInput && !cartSizeInput.value) {
                cartSizeInput.value = sizeInputs[0].value;
            }
        sizeInputs.forEach(input => {
            input.addEventListener('change', function() {
                    if (this.checked && cartSizeInput) {
                        cartSizeInput.value = this.value;
                }
            });
        });
        } else if (cartSizeInput && !cartSizeInput.value) {
            cartSizeInput.value = 'Standard';
        }

        // Form submission validation
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
            
            const selectedSize = document.querySelector('input[name="size"]:checked');
                if (sizeInputs.length > 0 && !selectedSize) {
                e.preventDefault();
                alert('Please select a size');
                return false;
            }
                if (cartQuantity) {
                    cartQuantity.value = quantityInput ? quantityInput.value : 1;
                }
            });
        }
    });
</script>
@endsection
