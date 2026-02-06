@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper d-none d-lg-block" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Our Products</h1>
            <p class="breadcumb-text">Browse Our Wide Selection Of Premium School Uniforms And Accessories</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><a href="{{ route('frontend.parent.dashboard', ['student_id' => $selectedProfile['id'] ?? '']) }}">Parent Dashboard</a></li>
                    <li>Our Products</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Breadcrumb (Compact) -->
<div class="breadcumb-wrapper d-lg-none" data-bg-src="{{ asset('assets/img/contact/Background.png') }}" style="padding-top: 80px; padding-bottom: 30px; min-height: auto; margin-top: 0px;">
    <div class="container z-index-common">
        <div class="breadcumb-content text-start">
            <ul class="breadcumb-menu justify-content-start" style="margin-bottom: 0;">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li>Store</li>
            </ul>
        </div>
    </div>
</div>

<section class="vs-product-wrapper margin-top-2 space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">

        <!-- Page Header -->
        <div class="row mb-4 mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="h3 mb-2">Store</h2>
                        <p class="text-muted mb-0">Shopping for {{ $selectedProfile['student_name'] }}</p>
                    </div>
                    <a href="{{ route('frontend.parent.dashboard', ['student_id' => $selectedProfile['id']]) }}" class="vs-btn btn-sm d-none d-lg-inline-flex">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                    <button class="vs-btn btn-sm d-lg-none style-outline" id="toggleFilters" data-bs-toggle="modal" data-bs-target="#filterModal" style="padding: 10px 20px; width: auto; border: 1px solid #490D59; color: #490D59; background: transparent;">
                        <i class="fas fa-sliders-h me-2"></i> <span>Filter</span>
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Sidebar Filters -->
            <div class="col-lg-3 mb-lg-0">

                <div class="filter-sidebar" id="filterSidebar">
                    <div class="filter-header">
                        <i class="fas fa-filter me-2"></i>
                        <h5 class="mb-0">Filters</h5>
                    </div>

                    <!-- Search -->
                    <div class="filter-section">
                        <div class="search-box">
                            <input type="text" id="productSearch" class="form-control" placeholder="Search...">
                            <button type="button" class="search-clear" id="clearSearch" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>




                    <!-- Product Type -->
                    <div class="filter-section">
                        <h6 class="filter-title">Product Type</h6>
                        <div class="filter-options">
                            @foreach($productTypes as $typeObj)
                                @php
                                    $typeSlug = $typeObj->slug;
                                    $label = $typeObj->label;
                                    

                                    
                                    // Default checked for all displayed types (including new ones)
                                    $isChecked = true;
                                    
                                    // Helper: skip bts and merch for filters as requested
                                    // if(in_array($typeSlug, ['merchandised', 'back_to_school'])) continue;
                                @endphp
                                <label class="filter-option">
                                    <input type="checkbox" name="product_type" value="{{ $typeSlug }}" class="filter-checkbox" {{ $isChecked ? 'checked' : '' }}>
                                    <span class="checkbox-mark"></span>
                                    <span class="option-label">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="filter-divider"></div>
                    </div>

                    <!-- Categories -->
                    <!-- Categories (Commented out as per request)
                    <div class="filter-section">
                        <h6 class="filter-title">Categories</h6>
                        <div class="filter-options">
                            @foreach($categories as $category)
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="{{ $category->slug }}" class="filter-checkbox" checked>
                                    <span class="checkbox-mark"></span>
                                    <span class="option-label">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    -->


                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="mb-3">
                    <p class="text-muted mb-0">Now Shopping By :</p>
                </div>

                <div class="row justify-content-start g-2 g-md-3" id="productsContainer">
                    @if(count($allProducts) > 0)
                        @foreach($allProducts as $product)
                            @php
                                $pType = strtolower($product['type'] ?? '');
                                // Fix for glitch: Don't even render these products to avoid flash
                                // if(in_array($pType, ['merchandised', 'back_to_school'])) continue;
                            @endphp
                            <div class="col-6 col-md-4 col-lg-3 product-item"
                                 data-product-type="{{ strtolower($product['type'] ?? '') }}"
                                 data-product-name="{{ strtolower($product['name']) }}"
                                 data-product-category="{{ strtolower($product['category'] ?? 'regular_uniforms') }}"
                                 data-product-gender="{{ strtolower($product['gender'] ?? 'unisex') }}">
                                <div class="vs-product product-style1 product-card-clickable"
                                     data-product-url="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}">
                                    <div class="product-img">

                                        <a href="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}" target="_blank">
                                            @if(isset($product['image']) && $product['image'])
                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                            @else
                                                <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $product['name'] }}" class="w-100">
                                            @endif
                                        </a>
                                        </a>
                                        @php
                                            $pType = isset($product['type']) ? strtolower($product['type']) : '';
                                            $pTypeObj = $productTypes->firstWhere('slug', $pType);
                                            // Show tag if product tag is set in DB, or fallback for authorized/optional
                                            $showTag = false;
                                            $tagName = '';
                                            
                                            if ($pTypeObj && !empty($pTypeObj->product_tag)) {
                                                $showTag = true;
                                                $tagName = $pTypeObj->product_tag;
                                            } elseif (in_array($pType, ['authorized', 'optional'])) {
                                                // Legacy fallback
                                                $showTag = true;
                                                $tagName = strtoupper($pType);
                                            }
                                        @endphp

                                        @if(isset($product['show_product_tag']) && $product['show_product_tag'] && !empty($product['product_tag']))
                                            <span class="product-tag-badge">{{ $product['product_tag'] }}</span>
                                        @elseif($showTag)
                                            <div class="product-tag {{ $pType }}">
                                                {{ $tagName }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-content">
                                        <span class="product-price">
                                            ₹{{ number_format($product['price']) }}
                                            @if(isset($product['original_price']) && $product['original_price'] > $product['price'])
                                                <del>₹{{ number_format($product['original_price']) }}</del>
                                            @endif
                                        </span>
                                        <h3 class="product-title">
                                            <a class="text-inherit" href="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}" target="_blank">
                                                {{ $product['name'] }}
                                            </a>
                                        </h3>
                                        <!-- <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                            <span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5</span>
                                        </div> -->
                                        <div class="actions">
                                            @if(isset($selectedProfile) && $selectedProfile)
                                                @php
                                                    $defaultSize = $product['sizes'][0] ?? 'Standard';
                                                @endphp
                                                <a href="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}" class="vs-btn w-100">
                                                    <i class="far fa-shopping-cart"></i>Choose Size
                                                </a>
                                            @else
                                                <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn flex-fill">
                                                    <i class="far fa-shopping-cart"></i>Select Profile
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Empty State -->
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                                <h4 class="mb-3" style="color: #333;">No Products Found</h4>
                                <p class="text-muted mb-4">
                                    We couldn't find any products matching your student profile.<br>
                                    Please check back later or contact support if you believe this is an error.
                                </p>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ route('frontend.parent.dashboard') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                    <a href="{{ route('frontend.shop.index') }}" class="btn btn-primary">
                                        <i class="fas fa-shopping-bag me-2"></i>Browse All Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="vs-pagination pt-40 text-center">
                    {{ $allProducts->links() }}
                </div>
        </div>
    </div>
</section>

<!-- Mobile Filter Modal -->
<div class="modal fade d-lg-none" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" style="z-index: 9999;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header" style="position: relative;">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="left: 15px; top: 50%; transform: translateY(-50%); opacity: 1;">
                    <i class="fas fa-arrow-left" style="font-size: 20px; color: #333;"></i>
                </button>
                <h5 class="modal-title ms-auto" id="filterModalLabel" style="margin-right: 15px;">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
            </div>
            <div class="modal-body">
                <!-- Search -->
                <div class="filter-section">
                    <div class="search-box">
                        <input type="text" id="productSearchMobile" class="form-control" placeholder="Search...">
                        <button type="button" class="search-clear" id="clearSearchMobile" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Type -->
                <div class="filter-section">
                    <h6 class="filter-title">Product Type</h6>
                    <div class="filter-options">
                        @foreach($productTypes as $typeObj)
                            @php
                                $typeSlug = $typeObj->slug;
                                $label = $typeObj->label;
                                

                                
                                // Default checked for all displayed types (including new ones)
                                $isChecked = true;
                                
                                // Helper: skip bts and merch for filters as requested
                                if(in_array($typeSlug, ['merchandised', 'back_to_school'])) continue;
                            @endphp
                            <label class="filter-option">
                                <input type="checkbox" name="product_type_mobile" value="{{ $typeSlug }}" class="filter-checkbox-mobile" {{ $isChecked ? 'checked' : '' }}>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">{{ $label }}</span>
                            </label>
                        @endforeach


                    </div>
                    <div class="filter-divider"></div>
                </div>

                <!-- Categories -->
                <!-- Categories (Commented out as per request)
                <div class="filter-section">
                    <h6 class="filter-title">Categories</h6>
                    <div class="filter-options">
                        @foreach($categories as $category)
                            <label class="filter-option">
                                <input type="checkbox" name="category_mobile" value="{{ $category->slug }}" class="filter-checkbox-mobile" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="applyFiltersMobile">
                    Apply Filters
                </button>
            </div>
        </div>     
    </div>
</div>

<style>
    .filter-sidebar {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 20px;
    }

    .filter-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0d5f0;
    }

    .filter-header h5 {
        color: #333;
        font-weight: 600;
    }

    .filter-header i {
        color: #490D59;
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-divider {
        height: 1px;
        background-color: #e0d5f0;
        margin: 20px 0;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #e0d5f0;
        border-radius: 8px;
        font-size: 14px;
    }

    .search-box input:focus {
        outline: none;
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }

    .search-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 5px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .filter-option {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        padding-left: 30px;
    }

    .filter-checkbox {
        position: absolute;
        opacity: 0 !important;
        cursor: pointer;
        height: 0;
        width: 0;
        margin: 0;
        padding: 0;
        z-index: -1;
        -webkit-appearance: none;
        appearance: none;
    }

    .checkbox-mark {
        position: absolute;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: #ffffff;
        border: 2px solid #ddd;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .filter-option:hover .checkbox-mark {
        border-color: #490D59;
    }

    .filter-checkbox:checked ~ .checkbox-mark {
        background-color: #490D59;
        border-color: #490D59;
    }

    .filter-checkbox:checked ~ .checkbox-mark:after {
        content: "✓";
        position: absolute;
        display: block;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .option-label {
        font-size: 14px;
        color: #333;
        margin-left: 10px;
    }

    .filter-checkbox:checked ~ .option-label {
        color: #490D59;
        font-weight: 500;
    }

    /* Mobile filter checkboxes - same styling as desktop */
    .filter-checkbox-mobile {
        position: absolute;
        opacity: 0 !important;
        cursor: pointer;
        height: 0;
        width: 0;
        margin: 0;
        padding: 0;
        z-index: -1;
        -webkit-appearance: none;
        appearance: none;
    }

    .filter-checkbox-mobile:checked ~ .checkbox-mark {
        background-color: #490D59;
        border-color: #490D59;
    }

    .filter-checkbox-mobile:checked ~ .checkbox-mark:after {
        content: "✓";
        position: absolute;
        display: block;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .filter-checkbox-mobile:checked ~ .option-label {
        color: #490D59;
        font-weight: 500;
    }

    /* Product Card Styles */
    .product-img {
        position: relative;
        overflow: hidden;
        border-radius: 30px 30px 0 0;
        background-color: #ffffff;
        width: 100%;
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 0;
    }

    .vs-product.product-style1 {
        display: flex;
        flex-direction: column;
        border: 3px solid var(--theme-color2, #e0d5f0);
        border-radius: 30px;
        transition: all ease 0.4s;
        overflow: hidden;
        background-color: #ffffff;
    }

    .vs-product.product-style1:hover {
        border-color: var(--theme-color, #490D59);
        transform: translateY(-5px);
        box-shadow: none;
    }

    .product-card-clickable {
        cursor: pointer;
        position: relative;
    }

    .product-card-clickable .actions,
    .product-card-clickable .actions * {
        position: relative;
        z-index: 10;
    }

    .product-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-price {
        font-size: 16px;
        font-weight: 600;
        color: #dc3545;
        font-family: var(--title-font, inherit);
        margin-bottom: 12px;
        display: block;
        line-height: 1;
    }

    .product-price del {
        font-size: 18px;
        color: #999;
        margin-left: 8px;
        font-weight: 400;
    }

    .product-title {
        font-size: 14px;
        margin-bottom: 5px;
        text-transform: capitalize;
        line-height: 1.4;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .product-title a {
        color: #333;
        text-decoration: none;
        font-size: 16px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-title a:hover {
        color: #490D59;
    }

    .star-rating {
        margin-bottom: 12px;
        font-size: 14px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        background: none !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    .star-rating span {
        display: block;
        color: transparent;
        font-size: 0;
        background: none !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        position: relative;
        width: auto;
        height: auto;
        line-height: 0;
    }

    .star-rating span:before {
        content: "\f005\f005\f005\f005\f005";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #ffb900;
        font-size: 14px;
        letter-spacing: 2px;
        background: none !important;
        display: block;
        line-height: 1;
        position: relative;
        z-index: 1;
    }

    .star-rating span strong {
        font-size: 0;
        line-height: 0;
        color: transparent;
        width: 0;
        height: 0;
        overflow: hidden;
        display: none;
    }

    .star-rating span {
        display: block;
        position: relative;
        height: 1em;
        line-height: 1;
        width: 5.4em;
    }

    .star-rating .rating {
        display: none;
    }

    .actions {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-style1 .vs-btn {
        background-color: var(--vs-secondary-color, #e0d5f0);
        padding: 17px 15px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .actions .icon-btn {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 50%;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .actions .icon-btn:hover {
        background-color: #490D59;
        border-color: #490D59;
        color: #ffffff;
    }

    .actions .icon-btn i {
        font-size: 16px;
    }

    .product-style1 .vs-btn:after,
    .product-style1 .vs-btn:before {
        background-color: var(--theme-color, #490D59);
    }

    .product-style1 .vs-btn i {
        margin-right: 10px;
    }

    .actions form {
        flex: 1;
    }

    .actions form .vs-btn {
        width: 100%;
        justify-content: center;
    }

    .product-item {
        display: flex;
        flex-direction: column;
        /* margin-bottom: 30px; */
        height: 100%;
    }

    .product-item .vs-product.product-style1 {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* @media (max-width: 767px) {
        .product-item {
            margin-bottom: 15px;
        }
    } */

    .product-item.hidden {
        display: none;
    }

    @media (max-width: 991px) {
        .filter-sidebar {
            position: relative;
            top: 0;
            display: none;
            margin-bottom: 20px;
            border: 2px solid #e0d5f0;
        }

        .filter-sidebar.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .filter-section {
            margin-bottom: 15px;
        }

        .product-img {
            height: 240px;
        }
    }

    @media (max-width: 575px) {
        .product-img {
            height: 200px;
            border-radius: 20px 20px 0 0;
        }

        .product-content {
            padding: 8px !important;

        }

        .actions {
            flex-direction: column;
        }

        .actions .icon-btn {
            width: 45px;
            height: 45px;
        }

        .product-title a {
            font-size: 14px;
        }

        .product-price {
            font-size: 14px;
            margin-bottom: 12px !important;
        }

        .product-style1 .vs-btn {
            font-size: 12px;
            padding: 12px 10px;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-tag-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: #ef4444;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-transform: uppercase;
    }

    /* Mobile Filter Modal Styles */
    #filterModal {
        z-index: 9999 !important;
    }

    #filterModal .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    #filterModal .modal-content {
        height: 100vh;
        border-radius: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: none;
    }

    #filterModal .modal-header {
        flex-shrink: 0;
        z-index: 1;
        background: #fff;
        border-bottom: 2px solid #e0d5f0;
        padding: 15px 20px;
    }

    #filterModal .modal-header .modal-title {
        color: #333;
        font-weight: 600;
    }

    #filterModal .modal-header .modal-title i {
        color: #490D59;
    }

    #filterModal .modal-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 20px;
    }

    #filterModal .modal-footer {
        flex-shrink: 0;
        z-index: 1;
        background: #fff;
        border-top: 2px solid #e0d5f0;
        padding: 15px 20px;
        display: flex;
        gap: 10px;
    }

    #filterModal .modal-footer .btn {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
    }

    #filterModal .modal-footer .btn-secondary {
        border: 1px solid #ddd;
        background: #fff;
        color: #333;
    }

    #filterModal .modal-footer .btn-primary {
        background: #490D59;
        border: none;
        color: white;
    }

    /* Modal backdrop */
    .modal-backdrop {
        z-index: 9998 !important;
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Ensure modal is above everything */
    .modal.show {
        display: block !important;
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    /* Ensure modal is above header and other elements */
    @media (max-width: 991px) {
        #filterModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
        }
    }


    /* Product Tag Styles */
    .product-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #000;
        color: #fff;
        padding: 4px 8px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 16px;
        z-index: 10;
        letter-spacing: 0.5px;
    }

    .product-tag.optional {
        background-color: #6c757d; /* Grey for optional */
    }

    /* Ensure product image container is relative */
    .product-img {
        position: relative;
    }

    /* Simplified Filter Button Styles */
    #toggleFilters {
        border: 2px solid #490D59 !important;
        background: white !important;
        color: #490D59 !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #toggleFilters:hover {
        background: #490D59 !important;
        color: white !important;
    }

    #toggleFilters.active-filter-btn {
        background: #490D59 !important;
        color: white !important;
    }

    .vs-btn.active-filter-btn,
    .vs-btn.active-filter-btn:hover {
        background-color: #490D59 !important;
        color: #ffffff !important;
        border-color: #490D59 !important;
    }

    .vs-btn.active-filter-btn span,
    .vs-btn.active-filter-btn i,
    .vs-btn.active-filter-btn:hover span,
    .vs-btn.active-filter-btn:hover i {
        color: #ffffff !important;
    }

    .vs-btn.active-filter-btn::before,
    .vs-btn.active-filter-btn::after,
    .vs-btn.active-filter-btn:hover::before,
    .vs-btn.active-filter-btn:hover::after {
        display: none !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...
    const productItems = document.querySelectorAll('.product-item');
    // ... existing code ...

    // Monitor for quantity selector appearance to toggle layout
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const target = mutation.target;
                // Check if we are inside .actions
                let actions = null;
                if (target.classList.contains('actions')) {
                    actions = target;
                } else if (target.closest('.actions')) {
                    actions = target.closest('.actions');
                }

                if (actions) {
                    const productItem = actions.closest('.product-item');
                    if (productItem) {
                        // Check for quantity input or specific class indicating expansion
                        // We check for text input quantity (visible) or specific container classes
                        if (actions.querySelector('input[name="quantity"][type="text"]') ||
                            actions.querySelector('input[name="quantity"][type="number"]') ||
                            actions.querySelector('.quantity') ||
                            actions.querySelector('.qty-input') ||
                            actions.querySelector('.quantity-plus')) {
                            productItem.classList.add('expanded-actions');
                        } else {
                            productItem.classList.remove('expanded-actions');
                        }
                    }
                }
            }
        });
    });

    // Observe all .actions containers
    document.querySelectorAll('.actions').forEach(function(actions) {
        observer.observe(actions, { childList: true, subtree: true });

        // Initial check
        const productItem = actions.closest('.product-item');
        if (productItem) {
            if (actions.querySelector('input[name="quantity"][type="text"]') ||
                actions.querySelector('input[name="quantity"][type="number"]') ||
                actions.querySelector('.quantity') ||
                actions.querySelector('.qty-input') ||
                actions.querySelector('.quantity-plus')) {
                productItem.classList.add('expanded-actions');
            }
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productItems = document.querySelectorAll('.product-item');
    const productTypeCheckboxes = document.querySelectorAll('input[name="product_type"]');
    const categoryCheckboxes = document.querySelectorAll('input[name="category"]');
    const genderCheckboxes = document.querySelectorAll('input[name="gender"]');
    const searchInput = document.getElementById('productSearch');
    const clearSearchBtn = document.getElementById('clearSearch');

    // Mobile filter elements
    const productTypeCheckboxesMobile = document.querySelectorAll('input[name="product_type_mobile"]');
    const categoryCheckboxesMobile = document.querySelectorAll('input[name="category_mobile"]');
    const searchInputMobile = document.getElementById('productSearchMobile');
    const clearSearchBtnMobile = document.getElementById('clearSearchMobile');
    const applyFiltersBtnMobile = document.getElementById('applyFiltersMobile');
    const filterModal = document.getElementById('filterModal');

    // Check if mobile view
    const isMobile = window.innerWidth < 992;

    function filterProducts(useMobileFilters = false) {
        let selectedTypes, selectedCategories, selectedGenders, searchTerm, hasAnyTypeChecked, hasAnyCategoryChecked, hasAnyGenderChecked;
        let visibleCategories = new Set();

        if (useMobileFilters && isMobile) {
            // Use mobile filter values
            selectedTypes = Array.from(productTypeCheckboxesMobile).filter(cb => cb.checked).map(cb => cb.value);
            selectedCategories = Array.from(categoryCheckboxesMobile).filter(cb => cb.checked).map(cb => cb.value);
            selectedGenders = []; // No gender filter in mobile modal for now
            searchTerm = searchInputMobile ? searchInputMobile.value.toLowerCase().trim() : '';
            hasAnyTypeChecked = productTypeCheckboxesMobile.length ? Array.from(productTypeCheckboxesMobile).some(cb => cb.checked) : false;
            hasAnyCategoryChecked = categoryCheckboxesMobile.length ? Array.from(categoryCheckboxesMobile).some(cb => cb.checked) : true; // Default to true if no filters exist
            hasAnyGenderChecked = false;
        } else {
            // Use desktop filter values
            selectedTypes = Array.from(productTypeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            selectedCategories = Array.from(categoryCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            selectedGenders = Array.from(genderCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            searchTerm = searchInput.value.toLowerCase().trim();
            hasAnyTypeChecked = productTypeCheckboxes.length ? Array.from(productTypeCheckboxes).some(cb => cb.checked) : false;
            hasAnyCategoryChecked = categoryCheckboxes.length ? Array.from(categoryCheckboxes).some(cb => cb.checked) : true; // Default to true if no filters exist
            hasAnyGenderChecked = genderCheckboxes.length ? Array.from(genderCheckboxes).some(cb => cb.checked) : false;
        }

        // If no product types are checked OR no categories are checked, hide all products
        if (!hasAnyTypeChecked || !hasAnyCategoryChecked) {
            productItems.forEach(item => {
                item.style.display = 'none';
                item.classList.add('hidden');
            });
            return; // Exit early - no products should be shown
        }

        productItems.forEach(item => {
            const productType = (item.getAttribute('data-product-type') || '').toLowerCase();
            const productName = (item.getAttribute('data-product-name') || '').toLowerCase();
            const productCategory = (item.getAttribute('data-product-category') || 'regular_uniforms').toLowerCase();
            const productGender = (item.getAttribute('data-product-gender') || 'unisex').toLowerCase();

            let show = true;

            // Filter by product type (must match at least one selected type)
            const matchesType = selectedTypes.some(type => {
                const typeLower = type.toLowerCase();
                // Handle different type formats
                if (typeLower === 'merchandised' && selectedTypes.some(t => t.toLowerCase() === 'merchandised') && (productType.includes('merchandise') || productType === 'merchandised')) {
                    return true;
                }
                if (typeLower === 'back_to_school' && selectedTypes.some(t => t.toLowerCase() === 'back_to_school') && (productType.includes('back') || productType.includes('school') || productType === 'back_to_school')) {
                    return true;
                }
                if (typeLower === 'authorized' && (productType === 'authorized' || productType.includes('authorized'))) {
                    return true;
                }
                if (typeLower === 'optional' && (productType === 'optional' || productType.includes('optional'))) {
                    return true;
                }
                return productType === typeLower;
            });
            if (!matchesType) {
                show = false;
            }

            // Collect categories that should be visible based on *checked product types*
            // We do this by checking if the product matches the currently selected TYPES.
            if (matchesType) {
                // This product is of a type that is currently checked.
                // So its category is valid and should be shown in the filters.
                visibleCategories.add(productCategory);
            }

            // Filter by category (must match at least one selected category)
            // Only apply if we actually have category filters active
            if (selectedCategories.length > 0 || (useMobileFilters && categoryCheckboxesMobile.length > 0) || (!useMobileFilters && categoryCheckboxes.length > 0)) {
                 const matchesCategory = selectedCategories.some(cat => {
                    const catLower = cat.toLowerCase();
                    // Try exact match first, then partial match
                    return productCategory === catLower || productCategory.includes(catLower) || catLower.includes(productCategory);
                });
                // If filters exist but don't match, hide. If no filters exist (checklist empty/hidden), allow.
                if (!matchesCategory && ((useMobileFilters && categoryCheckboxesMobile.length > 0) || (!useMobileFilters && categoryCheckboxes.length > 0))) {
                    show = false;
                }
            }


            // Filter by gender (optional - only filter if genders are checked)
            if (hasAnyGenderChecked) {
                const matchesGender = selectedGenders.some(gender => {
                    const genderLower = gender.toLowerCase();
                    return productGender === genderLower;
                });
                if (!matchesGender) {
                    show = false;
                }
            }

            // Filter by search term
            if (searchTerm && !productName.includes(searchTerm)) {
                show = false;
            }

            // Show or hide the product
            if (show) {
                item.style.display = '';
                item.classList.remove('hidden');
            } else {
                item.style.display = 'none';
                item.classList.add('hidden');
            }
        });

        // UPDATE CATEGORY VISIBILITY IN SIDEBAR
        // We hide category filters that don't have any products in the currently selected TYPES.
        const checkboxesToUpdate = useMobileFilters && isMobile ? categoryCheckboxesMobile : categoryCheckboxes;
        
        checkboxesToUpdate.forEach(cb => {
            const catSlug = cb.value.toLowerCase();
            const parentLabel = cb.closest('.filter-option');
            
            // Check if this category exists in our "visibleCategories" set
            // We use simple loose matching (includes) to be safe with slugs/names
            let isVisible = false;
            for (let validCat of visibleCategories) {
                 if (validCat === catSlug || validCat.includes(catSlug) || catSlug.includes(validCat)) {
                     isVisible = true;
                     break;
                 }
            }

            if (isVisible) {
                parentLabel.style.display = '';
            } else {
                parentLabel.style.display = 'none';
            }
        });
    }

    // Desktop: Apply filters immediately
    if (!isMobile) {
        productTypeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => filterProducts(false));
        });

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => filterProducts(false));
        });

        genderCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => filterProducts(false));
        });

        searchInput.addEventListener('input', function() {
            if (this.value.trim()) {
                clearSearchBtn.style.display = 'block';
            } else {
                clearSearchBtn.style.display = 'none';
            }
            filterProducts(false);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            filterProducts(false);
        });
    }

    // Mobile: Sync desktop filters to mobile when modal opens
    if (isMobile && filterModal) {
        filterModal.addEventListener('show.bs.modal', function() {
            // Sync desktop filter values to mobile filters
            productTypeCheckboxes.forEach((desktopCb) => {
                const matchingMobile = Array.from(productTypeCheckboxesMobile).find(mc => mc.value === desktopCb.value);
                if (matchingMobile) {
                    matchingMobile.checked = desktopCb.checked;
                    // Trigger change event to update visual state
                    matchingMobile.dispatchEvent(new Event('change'));
                }
            });
            categoryCheckboxes.forEach((desktopCb) => {
                const matchingMobile = Array.from(categoryCheckboxesMobile).find(mc => mc.value === desktopCb.value);
                if (matchingMobile) {
                    matchingMobile.checked = desktopCb.checked;
                    // Trigger change event to update visual state
                    matchingMobile.dispatchEvent(new Event('change'));
                }
            });
            if (searchInput && searchInputMobile) {
                searchInputMobile.value = searchInput.value;
                clearSearchBtnMobile.style.display = searchInputMobile.value.trim() ? 'block' : 'none';
            }
        });

        // Also ensure all checkboxes are checked on initial load
        filterModal.addEventListener('shown.bs.modal', function() {
            // Force update visual state of all checkboxes
            productTypeCheckboxesMobile.forEach(cb => {
                if (cb.checked) {
                    cb.dispatchEvent(new Event('change'));
                }
            });
            categoryCheckboxesMobile.forEach(cb => {
                if (cb.checked) {
                    cb.dispatchEvent(new Event('change'));
                }
            });
        });
    }

    // Mobile: Apply filters only when Apply button is clicked
    if (isMobile && applyFiltersBtnMobile) {
        applyFiltersBtnMobile.addEventListener('click', function() {
            // Sync mobile filter values to desktop filters
            productTypeCheckboxesMobile.forEach((mobileCb) => {
                const matchingDesktop = Array.from(productTypeCheckboxes).find(dc => dc.value === mobileCb.value);
                if (matchingDesktop) {
                    matchingDesktop.checked = mobileCb.checked;
                }
            });
            categoryCheckboxesMobile.forEach((mobileCb) => {
                const matchingDesktop = Array.from(categoryCheckboxes).find(dc => dc.value === mobileCb.value);
                if (matchingDesktop) {
                    matchingDesktop.checked = mobileCb.checked;
                }
            });
            if (searchInput && searchInputMobile) {
                searchInput.value = searchInputMobile.value;
            }

            // Apply filters
            filterProducts(true);

            // Close modal
            if (filterModal) {
                const bsModal = bootstrap.Modal.getInstance(filterModal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        });

        // Mobile search clear button
        if (searchInputMobile && clearSearchBtnMobile) {
            searchInputMobile.addEventListener('input', function() {
                clearSearchBtnMobile.style.display = this.value.trim() ? 'block' : 'none';
            });

            clearSearchBtnMobile.addEventListener('click', function() {
                searchInputMobile.value = '';
                this.style.display = 'none';
            });
        }
    }

    // Initialize filters
    filterProducts(false);

    // Make product cards clickable
    document.querySelectorAll('.product-card-clickable').forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on buttons or links
            if (e.target.closest('.actions') || e.target.closest('a')) {
                return;
            }

            const productUrl = this.getAttribute('data-product-url');
            if (productUrl) {
                window.open(productUrl, '_blank');
            }
        });
    });
});
</script>
@endsection
