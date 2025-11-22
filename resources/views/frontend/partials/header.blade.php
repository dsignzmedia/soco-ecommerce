<div class="preloader">
    <button class="vs-btn preloaderCls">Cancel Preloader </button>
    <div class="preloader-inner">
        <div class="loader"></div>
    </div>
</div>
<div class="vs-menu-wrapper">
    <div class="vs-menu-area text-center">
        <button class="vs-menu-toggle"><i class="fal fa-times"></i></button>
        <div class="mobile-logo">
            <a href="{{ route('frontend.index') }}"><img src="{{ asset('assets/img/logo.svg') }}" alt="Kiddino"></a>
        </div>
        <div class="vs-mobile-menu">
            <ul>
                <li class="menu-item-has-children">
                    <a href="{{ route('frontend.index') }}">Home</a>

                </li>
                <li>
                    <a href="{{ route('frontend.about-us') }}">About Us</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">Services</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">FAQ</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">Contact Us</a>
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
                                <li class="menu-item-has-children">
                                    <a href="#">Services</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">FAQ</a>
                                </li>
                                <li class="menu-item-has-children mega-menu-wrap">
                                    <a href="#">Contact Us</a>
                                </li>
                            </ul>
                        </nav>
                        <button class="vs-menu-toggle style6 d-inline-block d-lg-none"><i
                                class="fal fa-bars"></i></button>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-icons style2">
                            @if(request()->routeIs('frontend.parent.*'))
                                <!-- User Dropdown -->
                                <div class="dropdown">
                                    <a href="#" class="header-icon dropdown-toggle" data-bs-toggle="dropdown" style="text-decoration: none; color: #333;">
                                        <i class="fas fa-user" style="font-size: 20px;"></i>
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
                    @if(request()->routeIs('frontend.index') || request()->routeIs('frontend.about-us'))
                        <div class="col-auto d-none d-xl-block">
                            <a href="{{ route('frontend.get-started') }}" class="vs-btn">Login</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

