@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 mb-2">School Dashboard</h2>
                <p class="text-muted mb-0">Welcome, {{ session('school_username', 'School Admin') }}</p>
                @if(isset($schoolName) && $schoolName)
                    <h4 class="mt-2 mb-1" style="color: #490D59;">{{ $schoolName }}</h4>
                    @if(isset($schoolAddress) && $schoolAddress)
                        <p class="text-muted mb-0" style="font-size: 1rem; font-weight: 500;">{{ $schoolAddress }}</p>
                    @endif
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Dashboard Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #490D59 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Orders</h6>
                                <h3 class="mb-0" style="color: #490D59;">{{ number_format($dashboardData['total_orders'] ?? 0) }}</h3>
                            </div>
                            <div class="fs-1" style="color: #490D59; opacity: 0.3;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Revenue</h6>
                                <h3 class="mb-0" style="color: #28a745;">₹{{ number_format($dashboardData['total_revenue'] ?? 0) }}</h3>
                            </div>
                            <div class="fs-1" style="color: #28a745; opacity: 0.3;">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Pending Orders</h6>
                                <h3 class="mb-0" style="color: #ffc107;">{{ number_format($dashboardData['pending_orders'] ?? 0) }}</h3>
                            </div>
                            <div class="fs-1" style="color: #ffc107; opacity: 0.3;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Completed</h6>
                                <h3 class="mb-0" style="color: #17a2b8;">{{ number_format($dashboardData['completed_orders'] ?? 0) }}</h3>
                            </div>
                            <div class="fs-1" style="color: #17a2b8; opacity: 0.3;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Quick Actions Menu -->
            <div class="col-lg-3">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Quick Actions</h5>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <a href="{{ route('frontend.school.reports') }}" class="text-decoration-none d-flex align-items-center p-2 rounded" style="background-color: #f8f5ff; transition: all 0.3s;">
                                    <i class="fas fa-chart-line me-3" style="color: #490D59; width: 20px;"></i> 
                                    <span style="font-weight: 600;">Reports & Analytics</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none d-flex align-items-center p-2 rounded" style="transition: all 0.3s;">
                                    <i class="fas fa-shopping-cart me-3" style="color: #666; width: 20px;"></i> 
                                    <span>Orders Management</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none d-flex align-items-center p-2 rounded" style="transition: all 0.3s;">
                                    <i class="fas fa-users me-3" style="color: #666; width: 20px;"></i> 
                                    <span>Student Management</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none d-flex align-items-center p-2 rounded" style="transition: all 0.3s;">
                                    <i class="fas fa-box me-3" style="color: #666; width: 20px;"></i> 
                                    <span>Product Catalog</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none d-flex align-items-center p-2 rounded" style="transition: all 0.3s;">
                                    <i class="fas fa-cog me-3" style="color: #666; width: 20px;"></i> 
                                    <span>Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Welcome to School Portal</h5>
                        <p class="text-muted mb-4">Manage your school's uniform orders, view analytics, and generate comprehensive reports.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-4 rounded-4" style="background-color: #f8f5ff; border: 1px solid #e0d5f0;">
                                    <h6 class="mb-3"><i class="fas fa-chart-bar me-2" style="color: #490D59;"></i>View Reports</h6>
                                    <p class="text-muted small mb-3">Generate detailed reports with filters and visual analytics.</p>
                                    <a href="{{ route('frontend.school.reports') }}" class="vs-btn btn-sm">Go to Reports & Analytics</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4" style="background-color: #f8f5ff; border: 1px solid #e0d5f0;">
                                    <h6 class="mb-3"><i class="fas fa-shopping-bag me-2" style="color: #490D59;"></i>Manage Orders</h6>
                                    <p class="text-muted small mb-3">View and manage all uniform orders from parents.</p>
                                    <a href="#" class="vs-btn btn-sm">View Orders</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .list-unstyled a:hover {
        background-color: #f0f0f0 !important;
        transform: translateX(5px);
    }
</style>
@endsection

