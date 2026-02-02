@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcumb -->
<div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/breadcrumb/breadcumb-1.jpg') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Shop</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Shop</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="vs-product-wrapper space-top space-extra-bottom shop-page-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="h4 mb-0">Store</h2>
            <button class="vs-btn btn-sm style-outline d-lg-none" id="toggleFilters" data-bs-toggle="modal" data-bs-target="#filterModal" style="padding: 10px 20px; border: 1px solid #490D59; color: #490D59; background: transparent;">
                <i class="fas fa-sliders-h me-2"></i> <span>Filter</span>
            </button>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-xl-3 mb-lg-0 d-none d-lg-block">
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
                            <label class="filter-option">
                                <input type="checkbox" name="product_type" value="merchandised" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Merchandised Product</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="product_type" value="back_to_school" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Back to School Product</span>
                            </label>
                        </div>
                        <div class="filter-divider"></div>
                    </div>

                    <!-- Categories -->
                    <div class="filter-section">
                        <h6 class="filter-title">Categories</h6>
                        <div class="filter-options">
                            @foreach($categories as $cat)
                                @php
                                    $displayName = ucwords(str_replace('_', ' ', $cat));
                                @endphp
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="{{ strtolower($cat) }}" class="filter-checkbox" checked>
                                    <span class="checkbox-mark"></span>
                                    <span class="option-label">{{ $displayName }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9 col-xl-9">
                <div class="vs-sort-bar d-flex justify-content-between align-items-center mb-3">
                    <p class="woocommerce-result-count mb-0">
                        Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} results
                    </p>
                </div>

                <div class="row justify-content-start align-items-stretch" id="productsContainer">
                    @forelse($products as $product)
                    <div class="col-6 col-md-6 col-lg-4 col-xl-4 product-item"
                         data-product-type="{{ strtolower($product->product_type ?? '') }}"
                         data-product-name="{{ strtolower($product->product_name) }}"
                         data-product-category="{{ strtolower($product->category ?? 'general') }}">
                        <div class="vs-product product-style1 h-100 d-flex flex-column product-card">
                            <div class="product-img">
                                <a href="{{ route('frontend.shop.detail', $product->id) }}">
                                    @if($product->featured_image)
                                        <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->product_name }}" class="w-100" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                    @else
                                        <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $product->product_name }}" class="w-100">
                                    @endif
                                </a>
                                {{-- Actions --}}
                                <div class="actions">
                                    <a href="{{ route('frontend.shop.detail', $product->id) }}" class="icon-btn"><i class="far fa-heart"></i></a>
                                    <a href="{{ route('frontend.shop.detail', $product->id) }}" class="icon-btn"><i class="far fa-shopping-cart"></i></a>
                                </div>
                            </div>
                            <div class="product-content flex-grow-1 d-flex flex-column">
                                <span class="product-price">
                                    @if($product->price_sale)
                                        ₹{{ number_format($product->price_sale, 2) }}
                                        <del>₹{{ number_format($product->price_regular, 2) }}</del>
                                    @else
                                        ₹{{ number_format($product->price_regular, 2) }}
                                    @endif
                                </span>
                                <h3 class="product-title">
                                    <a class="text-inherit" href="{{ route('frontend.shop.detail', $product->id) }}">
                                        {{ $product->product_name }}
                                    </a>
                                </h3>
                                <div class="actions mt-auto">
                                    <a href="{{ route('frontend.shop.detail', $product->id) }}" class="vs-btn w-100">
                                        <i class="far fa-shopping-cart"></i>Choose Size
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info">No products found.</div>
                    </div>
                    @endforelse
                </div>

                <div class="vs-pagination pt-40 text-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Filter Modal -->
<div class="modal fade d-lg-none" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <label class="filter-option">
                            <input type="checkbox" name="product_type_mobile" value="merchandised" class="filter-checkbox-mobile" checked>
                            <span class="checkbox-mark"></span>
                            <span class="option-label">Merchandised Product</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="product_type_mobile" value="back_to_school" class="filter-checkbox-mobile" checked>
                            <span class="checkbox-mark"></span>
                            <span class="option-label">Back to School Product</span>
                        </label>
                    </div>
                    <div class="filter-divider"></div>
                </div>

                <!-- Categories -->
                <div class="filter-section">
                    <h6 class="filter-title">Categories</h6>
                    <div class="filter-options">
                        @foreach($categories as $cat)
                            @php
                                $displayName = ucwords(str_replace('_', ' ', $cat));
                            @endphp
                            <label class="filter-option">
                                <input type="checkbox" name="category_mobile" value="{{ strtolower($cat) }}" class="filter-checkbox-mobile" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">{{ $displayName }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
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
        display: block;
        width: 100%;
        max-width: 100%;
        overflow: visible;
        z-index: 1;
        float: none;
        clear: none;
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
        height: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: -1;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        visibility: hidden;
        pointer-events: none;
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
        text-transform: capitalize;
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
        height: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: -1;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        visibility: hidden;
        pointer-events: none;
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

    .product-item {
        display: flex;
        flex-direction: column;
    }
    
    /* Prevent any product from appearing in filter column */
    .col-lg-3 .product-item,
    .col-lg-3 #productsContainer,
    .filter-sidebar .product-item {
        display: none !important;
    }

    .product-item.hidden {
        display: none !important;
    }

    /* Product Actions Button Styling */
    .product-content .actions {
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
        width: 100%;
    }

    .product-style1 .vs-btn:after,
    .product-style1 .vs-btn:before {
        background-color: var(--theme-color, #490D59);
    }

    .product-style1 .vs-btn i {
        margin-right: 10px;
    }

    .product-card {
        border: 3px solid var(--theme-color2, #e0d5f0);
        border-radius: 30px;
        transition: all ease 0.4s;
        overflow: hidden;
        background-color: #ffffff;
    }

    .product-card:hover {
        border-color: var(--theme-color, #490D59);
        transform: translateY(-5px);
        box-shadow: none;
    }

    .product-img {
        position: relative;
        overflow: hidden;
        border-radius: 20px 20px 0 0;
        background-color: #ffffff;
        width: 100%;
        height: 260px;
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

    .product-price {
        font-size: 16px;
        font-weight: 600;
        color: #dc3545;
        font-family: var(--title-font, inherit);
        margin-bottom: 4px;
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

    @media (max-width: 991px) {
        .breadcumb-title {
           
            margin: 0.2em 0 -0.4em !important;
        }
        .filter-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 20px;
            border: 2px solid #e0d5f0;
            display: none;
            width: 100%;
            max-width: 100%;
            float: none;
            clear: both;
        }
        
        .shop-page-section .row > .col-lg-3,
        .shop-page-section .row > .col-lg-9 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            float: none;
            clear: both;
        }

        .filter-sidebar.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .product-img {
            height: 220px;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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

    /* Desktop breadcrumb and spacing */
    @media (min-width: 768px) {
        .breadcrumb-wrapper,
        .breadcumb-wrapper {
            padding-top: 50px !important;
        }

        .shop-page-section.space-top {
            padding-top: 24px !important;
        }
    }

    /* Breadcrumb title margin for screens greater than 991px */
    @media (min-width: 992px) {
        .breadcumb-title {
            margin: 0.21em 0 -0.4em 0 !important;
        }
    }

    /* Mobile spacing fix - reduce 80px to 8px */
    @media (max-width: 767px) {
        .shop-page-section.space-top {
            padding-top: 8px !important;
        }

        .shop-page-section.space-extra-bottom {
            padding-bottom: 8px !important;
        }

        /* Compact breadcrumb for mobile */
        .breadcrumb-wrapper,
        .breadcumb-wrapper {
            padding-top: 50px !important;
            padding-bottom: 15px !important;
            min-height: auto !important;
        }

        .breadcumb-title {
            display: none;
            
        }

        .breadcumb-menu-wrap,
        .breadcrumb-menu-wrap {
            margin-top: 0 !important;
            min-height: auto !important;
        }

        .breadcumb-content,
        .breadcrumb-content {
            text-align: left;
        }

        .breadcumb-menu,
        .breadcrumb-menu {
            justify-content: flex-start;
            margin-bottom: 0;
        }

        /* Reduce product content padding on mobile */
        .product-style1 .product-content {
            padding: 8px !important;
        }

        /* Reduce Choose Size button font on mobile */
        .product-style1 .vs-btn {
            font-size: 12px;
            padding: 12px 10px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productItems = document.querySelectorAll('.product-item');
    const productTypeCheckboxes = document.querySelectorAll('input[name="product_type"]');
    const categoryCheckboxes = document.querySelectorAll('input[name="category"]');
    const searchInput = document.getElementById('productSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterSidebar = document.getElementById('filterSidebar');
    const toggleFiltersBtn = document.getElementById('toggleFilters');

    // Mobile filter elements
    const productTypeCheckboxesMobile = document.querySelectorAll('input[name="product_type_mobile"]');
    const categoryCheckboxesMobile = document.querySelectorAll('input[name="category_mobile"]');
    const searchInputMobile = document.getElementById('productSearchMobile');
    const clearSearchBtnMobile = document.getElementById('clearSearchMobile');
    const applyFiltersBtnMobile = document.getElementById('applyFiltersMobile');
    const filterModal = document.getElementById('filterModal');

    // Check if mobile view
    const isMobile = window.innerWidth < 992;

    // Check URL parameter for product_type filter
    const urlParams = new URLSearchParams(window.location.search);
    const urlProductType = urlParams.get('product_type');
    
    // If URL has product_type parameter, set the corresponding checkbox
    if (urlProductType) {
        // Set desktop checkboxes
        productTypeCheckboxes.forEach(cb => {
            if (cb.value === urlProductType) {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });
        
        // Set mobile checkboxes
        productTypeCheckboxesMobile.forEach(cb => {
            if (cb.value === urlProductType) {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });
    }

    function filterProducts(useMobileFilters = false) {
        let selectedTypes, selectedCategories, searchTerm, hasAnyTypeChecked, hasAnyCategoryChecked;

        if (useMobileFilters && isMobile) {
            // Use mobile filter values
            selectedTypes = Array.from(productTypeCheckboxesMobile).filter(cb => cb.checked).map(cb => cb.value);
            selectedCategories = Array.from(categoryCheckboxesMobile).filter(cb => cb.checked).map(cb => cb.value);
            searchTerm = searchInputMobile ? searchInputMobile.value.toLowerCase().trim() : '';
            hasAnyTypeChecked = productTypeCheckboxesMobile.length ? Array.from(productTypeCheckboxesMobile).some(cb => cb.checked) : false;
            hasAnyCategoryChecked = categoryCheckboxesMobile.length ? Array.from(categoryCheckboxesMobile).some(cb => cb.checked) : false;
        } else {
            // Use desktop filter values
            selectedTypes = Array.from(productTypeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            selectedCategories = Array.from(categoryCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            searchTerm = searchInput.value.toLowerCase().trim();
            hasAnyTypeChecked = productTypeCheckboxes.length ? Array.from(productTypeCheckboxes).some(cb => cb.checked) : false;
            hasAnyCategoryChecked = categoryCheckboxes.length ? Array.from(categoryCheckboxes).some(cb => cb.checked) : false;
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
            const productCategory = (item.getAttribute('data-product-category') || '').toLowerCase();

            let show = true;

            // Filter by product type (must match at least one selected type)
            const productTypeLower = productType.toLowerCase();
            const matchesType = selectedTypes.some(type => {
                const typeLower = type.toLowerCase();
                // Handle different type formats
                if (typeLower === 'merchandised' && (productTypeLower.includes('merchandise') || productTypeLower === 'merchandised')) {
                    return true;
                }
                if (typeLower === 'back_to_school' && (productTypeLower.includes('back') || productTypeLower.includes('school') || productTypeLower === 'back_to_school')) {
                    return true;
                }
                return productTypeLower === typeLower;
            });
            if (!matchesType) {
                show = false;
            }

            // Filter by category (must match at least one selected category)
            const productCategoryLower = productCategory.toLowerCase();
            const matchesCategory = selectedCategories.some(cat => {
                const catLower = cat.toLowerCase();
                return productCategoryLower === catLower || productCategoryLower.includes(catLower) || catLower.includes(productCategoryLower);
            });
            if (!matchesCategory) {
                show = false;
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
    }

    // Desktop: Apply filters immediately
    if (!isMobile) {
        productTypeCheckboxes.forEach(cb => cb.addEventListener('change', () => filterProducts(false)));
        categoryCheckboxes.forEach(cb => cb.addEventListener('change', () => filterProducts(false)));

        searchInput.addEventListener('input', function() {
            clearSearchBtn.style.display = this.value.trim() ? 'block' : 'none';
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
});
</script>

@endsection
