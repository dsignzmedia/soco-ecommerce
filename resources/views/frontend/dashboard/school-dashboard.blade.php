@extends('frontend.layouts.school')

@section('title', 'Dashboard Overview | School Portal')

@section('content')
<div class="container-fluid p-0">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #490D59 0%, #6b2180 100%); box-shadow: 0 10px 30px rgba(73, 13, 89, 0.2);">
                <div class="position-relative z-1">
                    <h2 class="h3 fw-bold mb-2">Welcome Back, {{ session('school_username', 'Administrator') }}!</h2>
                    <p class="mb-0 opacity-75">Here's what's happening in your school store today.</p>
                    
                    @if(isset($schoolName) && $schoolName)
                        <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-inline-block">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-3 me-3">
                                    <i class="fas fa-school" style="color: #490D59; font-size: 20px;"></i>
                                </div>
                                <div>
                                    <h5 class="m-0 fw-bold">{{ $schoolName }}</h5>
                                    @if(isset($schoolAddress) && $schoolAddress)
                                        <small class="opacity-75">{{ $schoolAddress }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Decorative Circle -->
                <div class="position-absolute bottom-0 end-0 translate-middle-y me-5 mb-n5 rounded-circle bg-white opacity-10" style="width: 300px; height: 300px;"></div>
                <div class="position-absolute top-0 end-0 mt-n4 me-n4 rounded-circle bg-white opacity-10" style="width: 150px; height: 150px;"></div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <!-- Total Revenue -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card p-4 rounded-4 bg-white h-100 border-0 shadow-sm transition-hover">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="fas fa-rupee-sign fa-lg"></i>
                    </div>
                    <div class="dropdown">
                         <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase fw-semibold font-sm mb-1">Total Revenue</h6>
                <h3 class="fw-bold mb-0 text-dark">₹{{ number_format($dashboardData['total_revenue'] ?? 0) }}</h3>
                <div class="mt-3 d-flex align-items-center font-sm">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>3.2%</span>
                    <span class="text-muted ms-2">vs last month</span>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card p-4 rounded-4 bg-white h-100 border-0 shadow-sm transition-hover">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3" style="color: #490D59 !important; background-color: rgba(73, 13, 89, 0.1) !important;">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase fw-semibold font-sm mb-1">Total Orders</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($dashboardData['total_orders'] ?? 0) }}</h3>
                <div class="mt-3 d-flex align-items-center font-sm">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    <span class="text-muted ms-2">new orders</span>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card p-4 rounded-4 bg-white h-100 border-0 shadow-sm transition-hover">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase fw-semibold font-sm mb-1">Pending</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($dashboardData['pending_orders'] ?? 0) }}</h3>
                <div class="mt-3 d-flex align-items-center font-sm">
                    <span class="text-warning fw-bold">Action Needed</span>
                    <span class="text-muted ms-2">dispatch pending</span>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card p-4 rounded-4 bg-white h-100 border-0 shadow-sm transition-hover">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase fw-semibold font-sm mb-1">Delivered</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($dashboardData['completed_orders'] ?? 0) }}</h3>
                <div class="mt-3 d-flex align-items-center font-sm">
                    <span class="text-success fw-bold">100%</span>
                    <span class="text-muted ms-2">completion rate</span>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .font-sm {
        font-size: 0.875rem;
    }
    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========================================
        // Back Button Logout Confirmation Logic
        // ========================================
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogoutBtn = document.getElementById('cancelLogout');
        const confirmLogoutBtn = document.getElementById('confirmLogout');
        
        if (logoutModal) {
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(event) {
                logoutModal.style.display = 'flex';
                history.pushState(null, null, location.href);
            });
            if (cancelLogoutBtn) {
                cancelLogoutBtn.addEventListener('click', function() {
                    logoutModal.style.display = 'none';
                });
            }
            if (confirmLogoutBtn) {
                confirmLogoutBtn.addEventListener('click', function() {
                    const logoutForm = document.querySelector('form[action*="logout"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.href = '{{ route("login") }}';
                    }
                });
            }
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.style.display = 'none';
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && logoutModal.style.display === 'flex') {
                    logoutModal.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
@endsection
