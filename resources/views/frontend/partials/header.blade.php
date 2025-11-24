<div class="vs-menu-wrapper">
    <div class="vs-menu-area text-center">
        <button class="vs-menu-toggle"><i class="fal fa-times"></i></button>
        <div class="mobile-logo">
            <a href="{{ route('frontend.index') }}"><img src="{{ asset('assets/img/logo.svg') }}" alt="Kiddino"></a>
        </div>
        <div class="vs-mobile-menu">
            <ul>
                <li class="menu-item-has-children">
                    <a href="{{ route('frontend.index') }}" class="{{ request()->routeIs('frontend.index') || request()->path() == '/' ? 'active' : '' }}">Home</a>

                </li>
                <li>
                    <a href="{{ route('frontend.about-us') }}">About Us</a>
                </li>
                <li>
                    <a href="{{ route('frontend.services') }}">Services</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="{{ route('frontend.faq') }}" class="{{ request()->routeIs('frontend.faq') ? 'active' : '' }}">FAQ</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="{{ route('frontend.contact') }}">Contact Us</a>
                </li>

            </ul>
        </div>
    </div>
</div>

<header class="vs-header header-layout6">
    <div class="header-top">
        <div class="container">
            <div class="row justify-content-between align-items-center">

                <div class="col-lg-auto text-center">
                    <div class="header-links style-white">
                        <ul>
                            <li class="d-none d-xl-inline-block"><i class="fas fa-mobile-alt"></i>
                                <a href="tel:+919994878486">+91 9994878486</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <div class="header-links v6 style-white">
                        <ul>
                            <li>
                                <ul class="social-links5">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom" style="border-bottom: 2px solid #490D59;">
        <div class="container">
            <div class="menu-area">
                <div class="row align-items-center justify-content-between">
                    <div class="col col-lg-auto">
                        <div class="header-logo">
                            <a href="{{ route('frontend.index') }}">
                                <img src="{{ asset('assets/img/logo.svg') }}" alt="logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-auto col-lg text-center">
                        <nav class="main-menu menu-style5 d-none d-lg-block">
                            <ul>
                                <li class="menu-item-has-children">
                                    <a href="{{ route('frontend.index') }}" class="{{ request()->routeIs('frontend.index') || request()->path() == '/' ? 'active' : '' }}">Home</a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.about-us') }}" class="{{ request()->routeIs('frontend.about-us') ? 'active' : '' }}">About Us</a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.services') }}" class="{{ request()->routeIs('frontend.services') ? 'active' : '' }}">Services</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="{{ route('frontend.faq') }}" class="{{ request()->routeIs('frontend.faq') ? 'active' : '' }}">FAQ</a>
                                </li>
                                <li class="menu-item-has-children mega-menu-wrap">
                                     <a href="{{ route('frontend.contact') }}" class="{{ request()->routeIs('frontend.contact') ? 'active' : '' }}">Contact Us</a>
                                 </li>
                            </ul>
                        </nav>
                        @if(request()->routeIs('frontend.parent.*'))
                            @php
                                $mobileCartCount = count(session('cart', []));
                                $mobileUserName = session('parent_name', 'Parent User');
                                $mobileUserEmail = session('parent_email', 'parent@example.com');
                            @endphp
                            <div class="dropdown d-inline-block d-lg-none">
                                <button class="btn p-0 border-0 bg-transparent mobile-sidebar-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="sidebar-avatar-image mobile-avatar">
                                        <img src="{{ asset('assets/img/profile_icon/man.svg') }}" alt="Parent Avatar">
                                    </div>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end mobile-parent-dropdown">
                                    <li class="px-3 py-2 border-bottom">
                                        <p class="mb-0 fw-semibold">{{ $mobileUserName }}</p>
                                        <small class="text-muted">{{ $mobileUserEmail }}</small>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('frontend.parent.dashboard') }}">
                                            <i class="fas fa-th-large me-2"></i> Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item position-relative" href="{{ route('frontend.parent.cart') }}">
                                            <i class="fas fa-shopping-cart me-2"></i> Cart
                                            @if($mobileCartCount > 0)
                                                <span class="badge rounded-pill bg-danger position-absolute top-50 translate-middle-y" style="right: 15px;">{{ $mobileCartCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('frontend.parent.orders') }}">
                                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('frontend.parent.addresses') }}">
                                            <i class="fas fa-map-marker-alt me-2"></i> My Address
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('frontend.get-started') }}">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <button class="vs-menu-toggle style6 d-inline-block d-lg-none"><i class="fal fa-bars"></i></button>
                        @endif
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-icons style2">
                            @if(request()->routeIs('frontend.parent.*'))
                                <!-- User Dropdown -->
                                <div class="dropdown">
                                    <a href="#" class="header-icon dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" style="text-decoration: none; color: #333;">
                                        <div class="sidebar-avatar-image header-avatar-image">
                                            <img src="{{ asset('assets/img/profile_icon/man.svg') }}" alt="Parent Avatar">
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('frontend.parent.dashboard') }}" style="padding: 10px 15px;">
                                                <i class="fas fa-th-large me-2" style="width: 20px;"></i> Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center position-relative" href="{{ route('frontend.parent.cart') }}" style="padding: 10px 15px;">
                                                <i class="fas fa-shopping-cart me-2" style="width: 20px;"></i> Cart
                                                @php
                                                    $cartCount = count(session('cart', []));
                                                @endphp
                                                @if($cartCount > 0)
                                                    <span class="badge rounded-pill" style="background-color: #dc3545; color: #ffffff; font-size: 0.7rem; padding: 2px 6px; position: absolute; right: 15px;">{{ $cartCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('frontend.parent.orders') }}" style="padding: 10px 15px;">
                                                <i class="fas fa-shopping-bag me-2" style="width: 20px;"></i> My Orders
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('frontend.parent.addresses') }}" style="padding: 10px 15px;">
                                                <i class="fas fa-map-marker-alt me-2" style="width: 20px;"></i> My Address
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('frontend.get-started') }}" style="padding: 10px 15px; background-color: #f8f9fa;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='#f8f9fa'">
                                                <i class="fas fa-sign-out-alt me-2" style="width: 20px;"></i> Logout
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <form id="logout-form" action="{{ route('frontend.school.login') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </div>
                    @if(!request()->routeIs('frontend.parent.*'))
                        <div class="col-auto d-none d-xl-block">
                            <a href="{{ route('frontend.get-started') }}" class="vs-btn">Login</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>


<style>
    .vs-header.header-layout6 {
        position: relative;
        width: 100%;
        background-color: #ffffff;
        box-shadow: none;
    }

    .vs-header.header-layout6 .header-top {
        background-color: #490D59;
        z-index: 2;
    }

    .vs-header.header-layout6 .header-bottom {
        background-color: #ffffff;
        z-index: 2;
    }

    .main-menu ul li a.active,
    .vs-mobile-menu ul li a.active,
    .menu-style5 ul li a.active,
    .vs-mobile-menu ul li a.active {
        color: #dc3545 !important;
        font-weight: 600 !important;
    }
    
    .main-menu ul li a:hover,
    .vs-mobile-menu ul li a:hover,
    .menu-style5 ul li a:hover {
        color: #dc3545 !important;
        transition: color 0.3s ease;
    }

    .header-avatar-image {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #f8f5ff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e0d5f0;
    }

    .header-avatar-image img {
        width: 32px;
        height: 32px;
    }

    .mobile-sidebar-trigger .sidebar-avatar-image {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #f8f5ff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e0d5f0;
    }

    .mobile-sidebar-trigger .sidebar-avatar-image img {
        width: 32px;
        height: 32px;
    }

    .mobile-parent-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        font-weight: 500;
    }

    .mobile-parent-dropdown .dropdown-item i {
        width: 18px;
        text-align: center;
        color: #490D59;
    }

    .mobile-parent-dropdown .dropdown-item.text-danger i {
        color: #dc3545;
    }
</style>

