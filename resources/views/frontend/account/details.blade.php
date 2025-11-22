@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0" style="background-color: #f5f5f5; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div class="card-body p-3">
                        <a href="{{ route('frontend.parent.dashboard') }}" class="d-block text-decoration-none mb-2 p-2 rounded" style="background-color: {{ request()->routeIs('frontend.parent.dashboard') ? '#28a745' : 'transparent' }}; color: {{ request()->routeIs('frontend.parent.dashboard') ? '#ffffff' : '#333' }}; transition: all 0.2s;">
                            <i class="fas fa-th-large me-2"></i> Dashboard
                        </a>
                        
                        <a href="{{ route('frontend.parent.cart') }}" class="d-block text-decoration-none mb-2 p-2 rounded position-relative" style="background-color: {{ request()->routeIs('frontend.parent.cart') ? '#28a745' : 'transparent' }}; color: {{ request()->routeIs('frontend.parent.cart') ? '#ffffff' : '#333' }}; transition: all 0.2s;">
                            <i class="fas fa-shopping-cart me-2"></i> Cart
                            @php
                                $cartCount = count(session('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="badge rounded-pill" style="background-color: #dc3545; color: #ffffff; font-size: 0.7rem; padding: 2px 6px; position: absolute; right: 8px; top: 50%; transform: translateY(-50%);">{{ $cartCount }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('frontend.parent.orders') }}" class="d-block text-decoration-none mb-2 p-2 rounded" style="background-color: {{ request()->routeIs('frontend.parent.orders') ? '#28a745' : 'transparent' }}; color: {{ request()->routeIs('frontend.parent.orders') ? '#ffffff' : '#333' }}; transition: all 0.2s;">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </a>
                        
                        <a href="{{ route('frontend.parent.addresses') }}" class="d-block text-decoration-none mb-2 p-2 rounded" style="background-color: {{ request()->routeIs('frontend.parent.addresses') ? '#28a745' : 'transparent' }}; color: {{ request()->routeIs('frontend.parent.addresses') ? '#ffffff' : '#333' }}; transition: all 0.2s;">
                            <i class="fas fa-map-marker-alt me-2"></i> My Address
                        </a>
                        
                        <hr class="my-2" style="border-color: #ddd;">
                        
                        <a href="{{ route('frontend.get-started') }}" class="d-block text-decoration-none p-2 rounded" style="background-color: {{ request()->routeIs('frontend.get-started') ? '#e9ecef' : 'transparent' }}; color: #333; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='{{ request()->routeIs('frontend.get-started') ? '#e9ecef' : 'transparent' }}'">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0" style="background-color: #ffffff; border-radius: 12px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="font-weight: 600; color: #333;">Information</h4>
                        
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ $parentPhone ?? '+91 9159413234' }}" placeholder="Phone">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="vs-btn" style="background-color: #ff6b35; border: none;">
                                        Update Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

