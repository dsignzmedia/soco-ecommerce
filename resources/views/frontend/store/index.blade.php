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
                    <button class="vs-btn btn-sm d-lg-none style-outline" id="toggleFilters" style="padding: 10px 20px; width: auto; border: 1px solid #490D59; color: #490D59; background: transparent;">
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
                            <!-- Hidden filters for Authorized and Optional (checked by default) -->
                            <input type="checkbox" name="product_type" value="authorized" class="filter-checkbox d-none" checked>
                            <input type="checkbox" name="product_type" value="optional" class="filter-checkbox d-none" checked>

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
                
                <div class="row justify-content-start" id="productsContainer">
                    @foreach($allProducts as $product)
                        <div class="col-6 col-md-6 col-lg-4 col-xl-4 product-item" 
                             data-product-type="{{ $product['type'] }}"
                             data-product-name="{{ strtolower($product['name']) }}"
                             data-product-category="{{ $product['category'] ?? 'regular_uniforms' }}"
                             data-product-gender="{{ $product['gender'] ?? 'unisex' }}">
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
                                    @if(isset($product['type']) && in_array(strtolower($product['type']), ['authorized', 'optional']))
                                        <div class="product-tag {{ strtolower($product['type']) }}">
                                            {{ strtoupper($product['type']) }}
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
        min-height: 42px; /* Minimum height for 2 lines */
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
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
        display: block;
        margin-bottom: 30px;
    }

    @media (max-width: 767px) {
        .product-item {
            margin-bottom: 15px;
        }
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
            margin-bottom: 0 !important;
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
        to {
            opacity: 1;
            transform: translateY(0);
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

    function filterProducts() {
        const selectedTypes = Array.from(productTypeCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const selectedCategories = Array.from(categoryCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        const selectedGenders = Array.from(genderCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const searchTerm = searchInput.value.toLowerCase().trim();

        // Get all checkboxes to check if any are checked
        const allTypeCheckboxes = Array.from(productTypeCheckboxes);
        const allCategoryCheckboxes = Array.from(categoryCheckboxes);
        const allGenderCheckboxes = Array.from(genderCheckboxes);
        const hasAnyTypeChecked = allTypeCheckboxes.some(cb => cb.checked);
        const hasAnyCategoryChecked = allCategoryCheckboxes.some(cb => cb.checked);
        const hasAnyGenderChecked = allGenderCheckboxes.some(cb => cb.checked);

        productItems.forEach(item => {
            const productType = item.getAttribute('data-product-type');
            const productName = item.getAttribute('data-product-name') || '';
            const productCategory = item.getAttribute('data-product-category') || 'regular_uniforms';
            const productGender = item.getAttribute('data-product-gender') || 'unisex';

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

            // Filter by gender (multi-select)
            if (hasAnyGenderChecked) {
                if (!selectedGenders.includes(productGender)) {
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

    genderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });

    // Initial filter run
    filterProducts();

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
            this.classList.toggle('active-filter-btn');
            
            const label = this.querySelector('span');
            
            if (filterSidebar.classList.contains('active')) {
                label.textContent = 'Close';
                // Remove inline styles as class handles it
                this.style.backgroundColor = '';
                this.style.color = '';
            } else {
                label.textContent = 'Filter';
                this.style.backgroundColor = '';
                this.style.color = '';
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
