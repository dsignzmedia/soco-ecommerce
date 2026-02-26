@php
$userName = session('parent_name', 'Parent User');
$userEmail = session('parent_email', 'parent@example.com');
$sidebarItems = [
['route' => 'frontend.parent.dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Dashboard', 'active_routes' => 'frontend.parent.dashboard'],
['route' => 'frontend.parent.cart', 'icon' => 'fas fa-shopping-cart', 'label' => 'Cart', 'active_routes' => 'frontend.parent.cart'],
['route' => 'frontend.parent.orders', 'icon' => 'fas fa-shopping-bag', 'label' => 'My Orders', 'active_routes' => ['frontend.parent.orders', 'frontend.parent.track-order', 'frontend.parent.return-exchange']],
['route' => 'frontend.parent.profile', 'icon' => 'fas fa-user', 'label' => 'Profile', 'active_routes' => ['frontend.parent.profile', 'frontend.parent.addresses', 'frontend.parent.account', 'frontend.parent.edit-profile']],
];
$cartCount = Auth::check() ? \App\Models\Cart::where('user_id', Auth::id())->count() : 0;
@endphp

<div class="dashboard-sidebar-wrapper">
    <div class="dashboard-sidebar">
        <nav class="sidebar-menu">
            @foreach($sidebarItems as $item)
            @php
            $routeParams = [];
            if (isset($selectedProfile) && $selectedProfile) {
            $routeParams['profile_id'] = $selectedProfile['id'];
            }
            @endphp
            <a href="{{ route($item['route'], $routeParams) }}" class="sidebar-link {{ request()->routeIs($item['active_routes']) ? 'active' : '' }}">
                <span><i class="{{ $item['icon'] }} me-2"></i>{{ $item['label'] }}</span>
                @if($item['route'] === 'frontend.parent.cart' && $cartCount > 0)
                <span class="badge bg-danger">{{ $cartCount }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        <hr>

        <a href="{{ route('frontend.get-started') }}" class="sidebar-link logout-link mb-2">
            <span><i class="fas fa-sign-out-alt me-2"></i>Logout</span>
        </a>
    </div>
    @if(!request()->routeIs('frontend.parent.dashboard'))
    <div class="sidebar-remark mt-4 p-3 text-center" style="background-color: #f8f5ff; border: 1px solid #e0d5f0; border-radius: 12px;">
        <h6 style="color: #490D59; font-weight: 600; margin-bottom: 12px; font-size: 0.95rem;">Need to add more students?</h6>
        <a href="{{ route('frontend.parent.dashboard') }}" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); color: #ffffff; border-radius: 8px; font-weight: 500;">
            <li class="menu-item">
                <i class="fas fa-th-large me-2"></i>
                Dashboard
            </li>
        </a>
    </div>
    @endif
</div>


@once
<style>
    .dashboard-sidebar-wrapper {
        position: sticky;
        top: 120px;
    }

    .dashboard-sidebar {
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 27, 40, 0.08);
        padding: 20px;
    }

    .dashboard-sidebar .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .dashboard-sidebar .sidebar-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        border-radius: 10px;
        color: #4c5d73;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .dashboard-sidebar .sidebar-link i {
        width: 20px;
        text-align: center;
    }

    .dashboard-sidebar .sidebar-link.active,
    .dashboard-sidebar .sidebar-link:hover {
        background: #490D59;
        color: #ffffff;
        box-shadow: none;
    }

    .dashboard-sidebar .sidebar-link.active .badge,
    .dashboard-sidebar .sidebar-link:hover .badge {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .dashboard-sidebar .logout-link {
        color: #e74c3c;
        border: 1px solid rgba(231, 76, 60, 0.2);
    }

    .dashboard-sidebar .logout-link:hover {
        background-color: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    @media(max-width: 991px) {
        .dashboard-sidebar-wrapper {
            display: none;
        }
    }
</style>
@endonce