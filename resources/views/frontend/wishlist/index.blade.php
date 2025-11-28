@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px;  border-bottom: 1px solid #d0d0d0;">
    <div class="container" style=" padding: 20px;">
        <div class="breadcumb-menu-wrap" style=" margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li>Wishlist</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom parent-dashboard-wrapper" style="padding-top: 60px;">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4" style="background-color: #ffffff;">
                    <div class="card-body p-4">
                        <h2 class="mb-4">My Wishlist</h2>
                        <div class="woocommerce-cart-form">
                            <!-- Desktop View -->
                            <div class="d-none d-md-block">
                                <table class="cart_table mb-20">
                                    <thead>
                                        <tr>
                                            <th class="cart-col-image">Image</th>
                                            <th class="cart-col-productname">Product Name</th>
                                            <th class="cart-col-price">Price</th>
                                            <th class="cart-col-total">Stock Status</th>
                                            <th class="cart-col-remove">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($wishlist) > 0)
                                            @foreach($wishlist as $item)
                                                <tr class="cart_item">
                                                    <td data-title="Product">
                                                        <a class="cart-productimage" href="#"><img width="91" height="91" src="{{ $item['image'] }}" alt="Image"></a>
                                                    </td>
                                                    <td data-title="Name">
                                                        <a class="cart-productname" href="#">{{ $item['name'] }}</a>
                                                    </td>
                                                    <td data-title="Price">
                                                        <span class="amount"><bdi><span>₹</span>{{ $item['price'] }}</bdi></span>
                                                    </td>
                                                    <td data-title="Stock">
                                                        <span class="text-success">In Stock</span>
                                                    </td>
                                                    <td data-title="Remove">
                                                        <a href="{{ route('frontend.parent.remove-from-wishlist', ['productId' => $item['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}" class="remove" title="Remove this item"><i class="fal fa-trash-alt"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <!-- Empty State Only -->
                                             <tr class="cart_item">
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="fas fa-heart fa-3x text-muted"></i>
                                                    </div>
                                                    <p class="mb-4">No items in wishlist.</p>
                                                    @php
                                                        $profiles = session('student_profiles', []);
                                                        $firstProfileId = count($profiles) > 0 ? $profiles[0]['id'] : null;
                                                        $storeRoute = $firstProfileId ? route('frontend.parent.store', ['profile_id' => $firstProfileId]) : route('frontend.parent.dashboard');
                                                    @endphp
                                                    <a href="{{ $storeRoute }}" class="vs-btn">Go to Store</a>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile View -->
                            <div class="d-md-none">
                                @if(count($wishlist) > 0)
                                    <div class="row g-3">
                                        @foreach($wishlist as $item)
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm rounded-3" style="background-color: #f8f9fa;">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1 fw-bold text-dark">{{ $item['name'] }}</h6>
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <span class="text-primary fw-bold">₹{{ $item['price'] }}</span>
                                                                    <span class="badge bg-success bg-opacity-10 text-success">In Stock</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex-shrink-0 ms-3">
                                                                <a href="{{ route('frontend.parent.remove-from-wishlist', ['productId' => $item['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}" 
                                                                   class="btn btn-icon btn-sm btn-light text-danger border-0 shadow-sm" 
                                                                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%;"
                                                                   title="Remove">
                                                                    <i class="fal fa-trash-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-heart fa-3x text-muted"></i>
                                        </div>
                                        <p class="mb-4 text-muted">No items in wishlist.</p>
                                        @php
                                            $profiles = session('student_profiles', []);
                                            $firstProfileId = count($profiles) > 0 ? $profiles[0]['id'] : null;
                                            $storeRoute = $firstProfileId ? route('frontend.parent.store', ['profile_id' => $firstProfileId]) : route('frontend.parent.dashboard');
                                        @endphp
                                        <a href="{{ $storeRoute }}" class="vs-btn">Go to Store</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
