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
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-3 mb-40 mb-lg-0">
                <aside class="sidebar-area">
                    <div class="widget widget_categories">
                        <h3 class="widget_title">Categories</h3>
                        <ul>
                            <li><a href="{{ route('frontend.shop.index') }}">All Categories</a></li>
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('frontend.shop.index', ['category' => $cat]) }}" class="{{ request('category') == $cat ? 'text-theme' : '' }}">
                                        {{ $cat }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9 col-xl-9">
                <div class="vs-sort-bar">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-auto">
                            <div class="col-auto">
                                <p class="woocommerce-result-count">
                                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} results
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @forelse($products as $product)
                    <div class="col-sm-6 col-lg-4 col-xl-4 mb-30">
                        <div class="vs-product product-style1">
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
                            <div class="product-content">
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


@endsection
