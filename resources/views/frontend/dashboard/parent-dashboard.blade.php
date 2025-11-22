@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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
        @if(isset($profiles) && count($profiles) > 0)
            <!-- Tabbed Interface -->
            <div class="tabbed-interface-wrapper">
                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    @foreach($profiles as $profile)
                        <a href="{{ route('frontend.parent.dashboard', ['student_id' => $profile['id']]) }}" 
                           class="tab-button {{ (isset($selectedProfile) && $selectedProfile['id'] == $profile['id']) ? 'active' : '' }}">
                            {{ $profile['student_name'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('frontend.parent.create-profile') }}" class="tab-button add-tab">
                        <span>+</span>
                    </a>
                </div>

                @if(isset($selectedProfile))
                    <!-- Content Container -->
                    <div class="tab-content-container">
                        <div class="tab-content-inner">
                            <!-- Edit and Delete Buttons at the top of student card -->
                            <div class="d-flex justify-content-end gap-2 mb-3">
                                <a href="{{ route('frontend.parent.edit-profile', ['profileId' => $selectedProfile['id']]) }}" 
                                   class="btn btn-sm" 
                                   style="background-color: #28a745; color: #ffffff; border: none; padding: 8px 16px; border-radius: 6px;"
                                   title="Edit Profile">
                                    <i class="fas fa-edit me-2"></i> Edit Profile
                                </a>
                                <form action="{{ route('frontend.parent.delete-profile', ['profileId' => $selectedProfile['id']]) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this student profile? This action cannot be undone.');">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm" 
                                            style="background-color: #dc3545; color: #ffffff; border: none; padding: 8px 16px; border-radius: 6px;"
                                            title="Delete Profile">
                                        <i class="fas fa-trash me-2"></i> Delete Profile
                                    </button>
                                </form>
                            </div>

                            <!-- School and Student Info Card -->
                            <div class="card border-0 mb-4" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <!-- Left: School Logo and Info -->
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center gap-3">
                                                <div>
                                                    @if(isset($schoolLogo) && $schoolLogo)
                                                        <img src="{{ $schoolLogo }}" alt="{{ $selectedProfile['school_name'] }}" style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border: 1px solid #ddd;">
                                                            <i class="fas fa-school text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="mb-1" style="color: #333; font-weight: 600;">{{ $selectedProfile['school_name'] }}</h5>
                                                    @if(isset($schoolAddress) && $schoolAddress)
                                                        <p class="mb-0 text-muted small">{{ $schoolAddress }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Right: Student Info -->
                                        <div class="col-md-6 mt-3 mt-md-0">
                                            <div class="bg-light p-3 rounded" style="border-left: 3px solid #28a745;">
                                                <p class="mb-1 small"><strong>Student:</strong> {{ $selectedProfile['student_name'] }}</p>
                                                <p class="mb-1 small"><strong>Grade:</strong> {{ $selectedProfile['grade'] }} | <strong>Gender:</strong> {{ ucfirst($selectedProfile['gender']) }}</p>
                                                <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="btn btn-sm mt-2" style="background-color: #ff6b35; color: #ffffff; border: none;">
                                                    <i class="fas fa-shopping-bag me-1"></i> Shop Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchased Products Section -->
                            <div class="mb-3">
                                <h5 class="mb-3" style="color: #333; font-weight: 600;">Purchased Products</h5>
                            </div>
                            
                            @if(isset($purchasedProducts) && count($purchasedProducts) > 0)
                                <div class="row g-3">
                                    @foreach(array_slice($purchasedProducts, 0, 4) as $product)
                                        <div class="col-md-6">
                                            <div class="card border" style="border-radius: 8px;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex gap-3">
                                                        <div class="flex-shrink-0">
                                                            @if(isset($product['image']) && $product['image'])
                                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1" style="font-size: 0.95rem; font-weight: 600;">{{ $product['name'] }}</h6>
                                                            <p class="text-muted mb-0 small" style="font-size: 0.85rem;">{{ Str::limit($product['description'] ?? '', 50) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="card border-0 text-center py-5" style="background-color: #f8f9fa;">
                                    <div class="card-body">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">No purchased products yet.</p>
                                        <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="btn" style="background-color: #ff6b35; color: #ffffff; border: none;">
                                            <i class="fas fa-shopping-bag me-2"></i> Shop Now
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">Welcome to Your Dashboard</h4>
                            <p class="text-muted mb-4">Get started by creating a student profile to begin shopping for uniforms.</p>
                            <a href="{{ route('frontend.parent.create-profile') }}" class="vs-btn">
                                <i class="fas fa-plus me-2"></i> Add Student
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
            </div>
        </div>
    </div>
</section>

<style>
    .tabbed-interface-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .tab-navigation {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
        padding: 0;
    }

    .tab-button {
        padding: 12px 24px;
        font-weight: 500;
        text-decoration: none;
        border: 2px solid #e0d5f0;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
        position: relative;
        background-color: #e0d5f0;
        color: #333;
        display: inline-block;
    }

    .tab-button:hover {
        background-color: #d0c5e0;
        color: #333;
    }

    .tab-button.active {
        background-color: #ffffff;
        border-color: #490D59;
        border-bottom: 2px solid #ffffff;
        color: #333;
        z-index: 10;
        margin-bottom: -2px;
    }

    .tab-button.add-tab {
        padding: 12px 16px;
        font-size: 20px;
    }


    .tab-content-container {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 0 8px 8px 8px;
        padding: 24px;
        min-height: 400px;
    }

    .tab-content-inner {
        animation: fadeIn 0.3s ease-in-out;
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

    @media (max-width: 768px) {
        .tab-navigation {
            flex-wrap: wrap;
        }

        .tab-button {
            padding: 10px 16px;
            font-size: 0.9rem;
        }

        .tab-content-container {
            padding: 20px;
            border-radius: 8px;
        }

        .student-details-box {
            width: 100%;
            margin-top: 20px;
        }
    }
</style>
@endsection
