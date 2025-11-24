@php
    $userName = session('parent_name', 'Parent User');
    $userEmail = session('parent_email', 'parent@example.com');
    $sidebarItems = [
        ['route' => 'frontend.parent.dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Dashboard'],
        ['route' => 'frontend.parent.cart', 'icon' => 'fas fa-shopping-cart', 'label' => 'Cart'],
        ['route' => 'frontend.parent.orders', 'icon' => 'fas fa-shopping-bag', 'label' => 'My Orders'],
        ['route' => 'frontend.parent.addresses', 'icon' => 'fas fa-map-marker-alt', 'label' => 'My Address'],
    ];
    $cartCount = count(session('cart', []));
@endphp

<div class="dashboard-sidebar">
    <div class="sidebar-header d-flex align-items-center gap-3 mb-4">
        <div class="sidebar-avatar-image">
            <img src="{{ asset('assets/img/profile_icon/man.svg') }}" alt="Profile Icon">
        </div>
        <div>
            <h6 class="mb-0">{{ $userName }}</h6>
            <small class="text-muted">{{ $userEmail }}</small>
        </div>
    </div>

    <nav class="sidebar-menu">
        @foreach($sidebarItems as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                <span><i class="{{ $item['icon'] }} me-2"></i>{{ $item['label'] }}</span>
                @if($item['route'] === 'frontend.parent.cart' && $cartCount > 0)
                    <span class="badge bg-danger">{{ $cartCount }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <hr>

    <a href="{{ route('frontend.get-started') }}" class="sidebar-link logout-link">
        <span><i class="fas fa-sign-out-alt me-2"></i>Logout</span>
    </a>
</div>

@once
    <style>
        .dashboard-sidebar {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 27, 40, 0.08);
            padding: 20px;
            position: sticky;
            top: 120px;
        }

        .dashboard-sidebar .sidebar-header .sidebar-avatar-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f0f4ff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .dashboard-sidebar .sidebar-header .sidebar-avatar-image img {
            width: 40px;
            height: 40px;
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
            background-color: rgba(255,255,255,0.2);
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
            .dashboard-sidebar {
                display: none;
            }
        }
    </style>
@endonce

