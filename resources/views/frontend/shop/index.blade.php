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

<section class="vs-product-wrapper space-top space-extra-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="h4 mb-0">Store</h2>
            <button class="vs-btn btn-sm style-outline d-lg-none" id="toggleFilters" style="padding: 10px 20px; border: 1px solid #490D59; color: #490D59; background: transparent;">
                <i class="fas fa-sliders-h me-2"></i> <span>Filter</span>
            </button>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-xl-3 mb-40 mb-lg-0">
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
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="{{ strtolower($cat) }}" class="filter-checkbox" checked>
                                    <span class="checkbox-mark"></span>
                                    <span class="option-label">{{ $cat }}</span>
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
                    <div class="col-6 col-md-6 col-lg-4 col-xl-4 mb-30 product-item"
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
                                <h3 class="product-title"><a href="{{ route('frontend.shop.detail', $product->id) }}">{{ $product->product_name }}</a></h3>
                                <span class="price font-theme">
                                    @if($product->price_sale)
                                        <del>₹{{ number_format($product->price_regular, 2) }}</del> ₹{{ number_format($product->price_sale, 2) }}
                                    @else
                                        ₹{{ number_format($product->price_regular, 2) }}
                                    @endif
                                </span>
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


<style>
    .filter-sidebar {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 20px;
        display: block;
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

    .product-item {
        display: flex;
        flex-direction: column;
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
        .filter-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 20px;
            border: 2px solid #e0d5f0;
            display: none;
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

    function filterProducts() {
        const selectedTypes = Array.from(productTypeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        const selectedCategories = Array.from(categoryCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        const searchTerm = searchInput.value.toLowerCase().trim();

        const hasAnyTypeChecked = productTypeCheckboxes.length ? Array.from(productTypeCheckboxes).some(cb => cb.checked) : false;
        const hasAnyCategoryChecked = categoryCheckboxes.length ? Array.from(categoryCheckboxes).some(cb => cb.checked) : false;

        productItems.forEach(item => {
            const productType = (item.getAttribute('data-product-type') || '').toLowerCase();
            const productName = (item.getAttribute('data-product-name') || '').toLowerCase();
            const productCategory = (item.getAttribute('data-product-category') || '').toLowerCase();

            let show = true;

            if (hasAnyTypeChecked && !selectedTypes.includes(productType)) {
                show = false;
            }

            if (hasAnyCategoryChecked) {
                const matchesCategory = selectedCategories.some(cat => cat === productCategory);
                if (!matchesCategory) show = false;
            }

            if (searchTerm && !productName.includes(searchTerm)) {
                show = false;
            }

            item.classList.toggle('hidden', !show);
        });
    }

    productTypeCheckboxes.forEach(cb => cb.addEventListener('change', filterProducts));
    categoryCheckboxes.forEach(cb => cb.addEventListener('change', filterProducts));

    searchInput.addEventListener('input', function() {
        clearSearchBtn.style.display = this.value.trim() ? 'block' : 'none';
        filterProducts();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        filterProducts();
    });

    if (toggleFiltersBtn && filterSidebar) {
        toggleFiltersBtn.addEventListener('click', function() {
            filterSidebar.classList.toggle('active');
            const label = this.querySelector('span');
            if (filterSidebar.classList.contains('active')) {
                if (label) label.textContent = 'Close';
                this.style.backgroundColor = '#490D59';
                this.style.color = '#ffffff';
            } else {
                if (label) label.textContent = 'Filter';
                this.style.backgroundColor = 'transparent';
                this.style.color = '#490D59';
            }
        });
    }

    filterProducts();
});
</script>

@endsection
