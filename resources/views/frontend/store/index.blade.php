@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper " data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
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

<section class="vs-product-wrapper space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="h3 mb-2">Store</h2>
                        <p class="text-muted mb-0">Shopping for {{ $selectedProfile['student_name'] }}</p>
                    </div>
                    <a href="{{ route('frontend.parent.dashboard', ['student_id' => $selectedProfile['id']]) }}" class="vs-btn btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
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
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="d-lg-none mb-3">
                    <button class="vs-btn w-100" id="toggleFilters">
                        <i class="fas fa-sliders-h me-2"></i> <span>Show Filters</span>
                    </button>
                </div>
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
                                <input type="checkbox" name="product_type" value="authorized" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Authorized Products</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="product_type" value="optional" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Optional Products</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="product_type" value="merchandised" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Merchandised Products</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="product_type" value="back_to_school" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Back to School Products</span>
                            </label>
                        </div>
                    </div>


                    <!-- Categories -->
                    <div class="filter-section">
                        <h6 class="filter-title">Categories</h6>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="regular_uniforms" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Regular Uniforms</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="fabrics" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Fabrics</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="sports" class="filter-checkbox" checked>
                                <span class="checkbox-mark"></span>
                                <span class="option-label">Sports</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="mb-3">
                    <p class="text-muted mb-0">Now Shopping By :</p>
                </div>
                
                <div class="row justify-content-center" id="productsContainer">
                    @foreach($allProducts as $product)
                        <div class="col-md-6 col-lg-4 col-xl-4 product-item" 
                             data-product-type="{{ $product['type'] }}"
                             data-product-name="{{ strtolower($product['name']) }}"
                             data-product-category="{{ $product['category'] ?? 'regular_uniforms' }}">
                            <div class="vs-product product-style1 product-card-clickable" 
                                 data-product-url="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}">
                                <div class="product-img">
                                    <a href="{{ route('frontend.parent.product-detail', ['productId' => $product['id'], 'profile_id' => $selectedProfile['id']]) }}" target="_blank">
                                        @if(isset($product['image']) && $product['image'])
                                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-100">
                                        @else
                                            <img src="{{ asset('assets/img/product/product1-1.png') }}" alt="{{ $product['name'] }}" class="w-100">
                                        @endif
                                    </a>
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
                                    <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                        <span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5</span>
                                    </div>
                                    <div class="actions">
                                        @if(isset($selectedProfile) && $selectedProfile)
                                            @php
                                                $defaultSize = $product['sizes'][0] ?? 'Standard';
                                            @endphp
                                            <form action="{{ route('frontend.parent.add-to-cart') }}" method="POST" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
                                                <input type="hidden" name="size" value="{{ $defaultSize }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="vs-btn w-100">
                                                    <i class="far fa-shopping-cart"></i>Add to Cart
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn flex-fill">
                                                <i class="far fa-shopping-cart"></i>Select Profile
                                            </a>
                                        @endif
                                        <a href="#" class="icon-btn"><i class="far fa-heart"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($allProducts) === 0)
                    <div class="text-center py-5">
                        <p class="text-muted">No products found in this category.</p>
                    </div>
                @endif

                <!-- Pagination -->
                <div class="vs-pagination">
                    <a href="#" class="pagi-btn">Prev</a>
                    <ul>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">...</a></li>
                        <li><a href="#">12</a></li>
                    </ul>
                    <a href="#" class="pagi-btn">next</a>
                </div>
            </div>
        </div>
    </div>
</section>

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
        opacity: 0;
        cursor: pointer;
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
        padding: 15px;
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
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-price {
        font-size: 22px;
        font-weight: 500;
        color: #dc3545;
        font-family: var(--title-font, inherit);
        margin-bottom: 8px;
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
        padding: 17px 26px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
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
        display: block;
        margin-bottom: 30px;
    }

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
            padding: 16px;
        }

        .actions {
            flex-direction: column;
        }

        .actions .icon-btn {
            width: 45px;
            height: 45px;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productItems = document.querySelectorAll('.product-item');
    const productTypeCheckboxes = document.querySelectorAll('input[name="product_type"]');
    const categoryCheckboxes = document.querySelectorAll('input[name="category"]');
    const searchInput = document.getElementById('productSearch');
    const clearSearchBtn = document.getElementById('clearSearch');

    function filterProducts() {
        const selectedTypes = Array.from(productTypeCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const selectedCategories = Array.from(categoryCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const searchTerm = searchInput.value.toLowerCase().trim();

        // Get all checkboxes to check if any are checked
        const allTypeCheckboxes = Array.from(productTypeCheckboxes);
        const allCategoryCheckboxes = Array.from(categoryCheckboxes);
        const hasAnyTypeChecked = allTypeCheckboxes.some(cb => cb.checked);
        const hasAnyCategoryChecked = allCategoryCheckboxes.some(cb => cb.checked);

        productItems.forEach(item => {
            const productType = item.getAttribute('data-product-type');
            const productName = item.getAttribute('data-product-name') || '';
            const productCategory = item.getAttribute('data-product-category') || 'regular_uniforms';

            let show = true;

            // Filter by product type (multi-select)
            // If any type checkbox is checked, show only products matching selected types
            // If no type checkbox is checked, show all products (don't filter by type)
            if (hasAnyTypeChecked) {
                if (!selectedTypes.includes(productType)) {
                    show = false;
                }
            }

            // Filter by category (multi-select)
            // If any category checkbox is checked, show only products matching selected categories
            // If no category checkbox is checked, show all products (don't filter by category)
            if (hasAnyCategoryChecked) {
                if (!selectedCategories.includes(productCategory)) {
                    show = false;
                }
            }

            // Filter by search term
            if (searchTerm && !productName.toLowerCase().includes(searchTerm)) {
                show = false;
            }

            // Show or hide the product
            if (show) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Event listeners
    productTypeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });

    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });

    searchInput.addEventListener('input', function() {
        if (this.value.trim()) {
            clearSearchBtn.style.display = 'block';
        } else {
            clearSearchBtn.style.display = 'none';
        }
        filterProducts();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        filterProducts();
    });

    const filterSidebar = document.getElementById('filterSidebar');
    const toggleFiltersBtn = document.getElementById('toggleFilters');

    if (toggleFiltersBtn && filterSidebar) {
        toggleFiltersBtn.addEventListener('click', function() {
            filterSidebar.classList.toggle('active');
            const label = this.querySelector('span');
            if (filterSidebar.classList.contains('active')) {
                label.textContent = 'Hide Filters';
            } else {
                label.textContent = 'Show Filters';
            }
        });
    }

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
