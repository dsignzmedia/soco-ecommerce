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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0" style="font-weight: 600; color: #333;">My Addresses</h4>
                            <button type="button" class="btn" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 10px 20px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i> Add New Address
                            </button>
                        </div>
                        
                        @if(isset($savedAddresses) && count($savedAddresses) > 0)
                            <div class="row g-3">
                                @foreach($savedAddresses as $index => $address)
                                    <div class="col-md-6">
                                        <div class="card border position-relative" style="border-radius: 8px;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><strong>{{ $address['name'] }}</strong></h6>
                                                        <p class="text-muted small mb-1">{{ $address['phone'] }}</p>
                                                        <p class="text-muted small mb-2">{{ $address['email'] }}</p>
                                                    </div>
                                                    <div>
                                                        <form action="{{ route('frontend.parent.delete-address', ['addressId' => $index]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-sm" 
                                                                    style="background-color: #dc3545; color: #ffffff; border: none; padding: 6px 10px; border-radius: 6px;"
                                                                    title="Delete Address">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="mb-2 small">{{ $address['address'] }}</p>
                                                <p class="mb-0 small text-muted">{{ $address['city'] }}, {{ $address['state'] }} - {{ $address['pincode'] }}</p>
                                                @if(!empty($address['landmark']))
                                                    <p class="mb-0 small text-muted">Landmark: {{ $address['landmark'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-4">No addresses saved yet.</p>
                                <button type="button" class="btn" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 10px 20px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="fas fa-plus me-2"></i> Add New Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add New Address Modal (same as checkout) -->
@include('frontend.checkout.add-address-modal')
@endsection

