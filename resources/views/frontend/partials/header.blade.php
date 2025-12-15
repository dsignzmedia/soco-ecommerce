@php
    $headerRouteParams = [];
    // Try to get profile from request first, then fallback to session
    $headerProfileId = request('profile_id');
    if (!$headerProfileId) {
        $headerProfiles = session('student_profiles', []);
        if (count($headerProfiles) > 0) {
            $headerProfileId = $headerProfiles[0]['id'];
        }
    }
    if ($headerProfileId) {
        $headerRouteParams['profile_id'] = $headerProfileId;
    }
@endphp


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
                    @auth
                        @if(Auth::user()->isMasterAdmin())
                            <li><a href="{{ route('master.admin.dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('master.admin.profile') }}">Profile</a></li>
                            <li>
                                <form action="{{ route('master.admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 700; color: var(--title-color); width: 100%; text-align: left;">Logout</button>
                                </form>
                            </li>
                        @elseif(Auth::user()->isInventoryAdmin())
                            <li><a href="{{ route('inventory.admin.dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('inventory.admin.profile') }}">Profile</a></li>
                            <li>
                                <form action="{{ route('inventory.admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 700; color: var(--title-color); width: 100%; text-align: left;">Logout</button>
                                </form>
                            </li>
                        @elseif(Auth::user()->isSchool())
                             <li><a href="{{ route('frontend.school.dashboard') }}">Dashboard</a></li>
                             <li>
                                <form action="{{ route('frontend.school.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 700; color: var(--title-color); width: 100%; text-align: left;">Logout</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('frontend.parent.dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('frontend.parent.profile') }}">Profile</a></li>
                            <li><a href="{{ route('frontend.parent.orders') }}">My Orders</a></li>
                            <li><a href="{{ route('frontend.parent.cart') }}">Cart</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; padding: 12px 0; font-size: 16px; font-weight: 700; color: var(--title-color); width: 100%; text-align: left;">Logout</button>
                                </form>
                            </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>

    <!-- Profile Sidebar Wrapper -->

    <header class="vs-header header-layout6">
        <div class="header-top">
            <div class="container">
                <div class="row justify-content-between align-items-center">

                    <div class="col-lg-auto text-center top-pad">
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
                    <div class="col-auto d-none d-lg-block pad-right">
                        <div class="header-links v6 style-white">
                            <ul>
                                <li>
                                    <ul class="social-links5">
                                        <li><a href="https://www.facebook.com/profile.php?id=6156745997746" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="https://www.youtube.com/@Socous" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                        <li><a href="https://www.instagram.com/socoproducts/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="https://www.linkedin.com/company/soco-products-privatelimited/?viewAsMember=true" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                        <li><a href="https://x.com/SoCoproducts" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
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
                                        <a href="{{ route('frontend.index') }}">Home</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('frontend.about-us') }}">About Us</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('frontend.services') }}">Services</a>
                                    </li>

                                    </li>
                                    <li class="menu-item-has-children">
                                        <a href="{{ route('frontend.faq') }}">FAQ</a>
                                    </li>
                                    <li class="menu-item-has-children mega-menu-wrap">
                                        <a href="{{ route('frontend.contact') }}">Contact Us</a>
                                    </li>
                                </ul>
                            </nav>
                            <button class="vs-menu-toggle style6 d-inline-block d-lg-none"><i
                                    class="fal fa-bars"></i></button>
                        </div>
                        <div class="col-auto  d-none d-lg-block">
                            <div class="header-icons style2">

                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-block" style="padding-right: 15px !important;">
                            @auth
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: #490D59; font-size: 24px;">
                                        <i class="fas fa-user-circle"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <div class="dropdown-header">
                                                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                                @if(!str_contains(Auth::user()->email, '@noemail.com'))
                                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                                @else
                                                    <div class="small text-muted">{{ Auth::user()->phone }}</div>
                                                @endif
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        @if(Auth::user()->isMasterAdmin())
                                            <li><a class="dropdown-item" href="{{ route('master.admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                                            <li><a class="dropdown-item" href="{{ route('master.admin.profile') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('master.admin.logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                                </form>
                                            </li>
                                        @elseif(Auth::user()->isInventoryAdmin())
                                            <li><a class="dropdown-item" href="{{ route('inventory.admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                                            <li><a class="dropdown-item" href="{{ route('inventory.admin.profile') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('inventory.admin.logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                                </form>
                                            </li>
                                        @elseif(Auth::user()->isSchool())
                                            <li><a class="dropdown-item" href="{{ route('frontend.school.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('frontend.school.logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                                </form>
                                            </li>
                                        @else
                                            <li><a class="dropdown-item" href="{{ route('frontend.parent.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                                            <li><a class="dropdown-item" href="{{ route('frontend.parent.profile') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                                            <li><a class="dropdown-item" href="{{ route('frontend.parent.orders') }}"><i class="fas fa-box me-2"></i> My Orders</a></li>
                                            <li><a class="dropdown-item" href="{{ route('frontend.parent.cart') }}"><i class="fas fa-shopping-cart me-2"></i> Cart</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="vs-btn">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            
            </style>
            
    </header>


    <style>
        /* Header padding adjustments - more specific selectors */
            .vs-header .header-top .row.top-pad {
                padding-left: 60px !important;
            }
            
            .vs-header .header-top .col-lg-auto.top-pad {
                padding-left: 60px !important;
            }

            .vs-header .header-top .col-auto.pad-right {
                padding-right: 32px !important;
            }
            @media (max-width: 991px) {
                .header-layout6 .header-bottom::after, .header-layout7 .header-bottom::after {
                height: 30px; }
            }



            .category-box {
                width: 120px;
                height: 120px;
                background-color: #f8f5ff;
                border: 2px solid #e0d5f0;
                border-radius: 60px !important; /* <-- THIS BREAKS THE CIRCLE */
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            /* Image fill */
            .category-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform .4s ease;
            }
            
            /* Zoom on hover */
            .category-item:hover .category-img {
                transform: scale(1.15);
            }
            
            /* Name */
            .category-name {
                color: #333;
                font-weight: 500;
                margin: 0;
                margin-top: 8px;
            }
        @media (max-width: 991px) {
            .header-layout6 .header-bottom::after, .header-layout7 .header-bottom::after {
                height: 30px;
            }
            .header-layout6 .vs-btn, .header-layout7 .vs-btn {
                padding: 10px 20px !important;
                font-size: 14px;
                height: 40px;
                line-height: 40px;
            }
        }

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
            background-color: #490D59;
            z-index: 2;
            height: 30px;
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

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .mobile-parent-dropdown {
            position: fixed !important;
            top: auto !important;
            left: 50% !important;
            right: 50% !important;
            width: 50% !important; /* Full width */
            min-width: 50% !important;
            max-width: 50% !important; /* Full screen width */
            margin: 0 !important;
            border-radius: 0 !important;
            transform: none !important; /* Reset transform for normal state */
            border: none !important;
            border-top: 1px solid #eee !important;
            box-shadow: -5px 10px 30px rgba(0,0,0,0.1) !important;
            margin-top: 0 !important;
            display: none; /* Default hidden */
        }

        .mobile-parent-dropdown.show {
            display: block !important;
            animation: slideInRight 0.3s ease-out forwards; /* Animate when shown */
        }

        .mobile-parent-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: flex-start !important; /* Fix spacing issue */
            gap: 8px; /* Reduced gap */
            padding: 10px 15px; /* Reduced padding */
            font-weight: 500;
            width: 100%;
            letter-spacing: normal !important; /* Ensure no weird letter spacing */
        }

        .mobile-parent-dropdown .dropdown-item i {
            width: 18px;
            text-align: center;
            color: #490D59;
        }

        .mobile-parent-dropdown .dropdown-item.text-danger i {
            color: #dc3545;
        }

        @media (max-width: 1399px) {
            .header-layout6 .menu-area {
                border-radius: 9999px !important;
                margin: 0 auto -40.5px auto !important;
            }
        }
      

    </style>

