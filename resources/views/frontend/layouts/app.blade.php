<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SoCo Uniforms</title>
    <!-- <meta name="author" content="Vecuro"> -->
    <meta name="description"
        content="we make school uniform shopping easy and hassle-free. As a leading uniform manufacturer in Coimbatore, we have been supplying high-quality school uniforms for years. Now, we're bringing our expertise online, making it simpler for parents to order uniforms with just a few clicks">
    <!-- <meta name="keywords" content="SoCo Uniforms - School Uniforms"> -->
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Place favicon.ico in the root directory -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}"> -->
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <!-- Layerslider -->
    <link rel="stylesheet" href="{{ asset('assets/css/layerslider.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <!-- Slick Slider -->
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        html,
        body {
            height: 100%;
            background-color: #ffffff;
        }

        .vs-hero-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('{{ asset('assets/img/hero/hero_crop.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .vs-hero-wrapper .ls-wp-container,
        .vs-hero-wrapper .ls-outer-container,
        .vs-hero-wrapper .ls-inner-container,
        .vs-hero-wrapper .ls-slide,
        .vs-hero-wrapper .ls-slide-backgrounds {
            min-height: 100vh !important;
            height: 100vh !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .vs-hero-wrapper .ls-slide .ls-bg {
            position: absolute !important;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }

        .hero-overlay {
            position: relative;
            transform: none;
            background: rgba(73, 13, 89, 0.88);
            color: #fff;
            text-align: center;
            padding: 3rem;
            border-radius: 48px;
            width: min(90%, 720px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .hero-overlay h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .hero-overlay .hero-subtitle {
            text-transform: uppercase;
            letter-spacing: 0.3em;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            color: #ffd6f4;
        }

        .hero-overlay .hero-text {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-overlay .hero-btn {
            padding-inline: 2.5rem;
        }

        @media (max-width: 776px) {
            .hero-overlay {
                width: 90%;
                padding: 2rem 1.5rem;
                border-radius: 32px;
            }

            .hero-overlay .hero-btn {
                display: inline-block;
                width: 100%;
            }
        }

        @media (max-width: 776px) {
            .vs-hero-wrapper {
                padding-top: 0;
                padding-bottom: 0;
                overflow: hidden;
                flex-direction: column;
                min-height: 60vh;
            }

            .vs-hero-wrapper .ls-wp-container,
            .vs-hero-wrapper .ls-outer-container,
            .vs-hero-wrapper .ls-inner-container,
            .vs-hero-wrapper .ls-slide,
            .vs-hero-wrapper .ls-slide-backgrounds {
                min-height: 60vh !important;
                height: 60vh !important;
            }

            .vs-hero-wrapper .ls-wp-container,
            .vs-hero-wrapper .ls-wp-container .ls-wrapper,
            .vs-hero-wrapper .ls-wp-container .ls-slide,
            .vs-hero-wrapper .ls-wp-container .ls-bg {
                width: 100% !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            .vs-hero-wrapper .ls-l {
                left: 50% !important;
                transform: translateX(-50%);
            }
        }
        @media (max-width: 768px) {
            /* Resize Menu Toggle Button Globally */
            .vs-menu-toggle {
                transform: scale(0.8);
                transform-origin: center;
            }
        }
    </style>

</head>

<body>
    @yield('content')



    <footer class="footer-wrapper footer-layout1" data-bg-src="{{ asset('assets/img/bg/footer-bg-1-1.png') }}">
        <div class="footer-top">
            <div class="container">
                <div class="row gx-60 gy-4 text-center text-lg-start justify-content-between align-items-center">
                    <div class="col-lg"><a href="{{ route('frontend.index') }}"><img
                                src="{{ asset('assets/img/logo_white.svg') }}" alt="logo"></a></div>
                    <div class="col-lg-auto">
                        <h3 class="h4 mb-0 text-white"><img src="{{ asset('assets/img/icon/check-list.svg') }}"
                                alt="icon" class="me-2"> Shop Your School's Uniforms in One Place</h3>
                    </div>
                    <div class="col-lg-auto"><a href="{{ route('frontend.get-started') }}" class="vs-btn">Shop Now</a></div>
                </div>
            </div>
        </div>
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-center gx-60">
                    <div class="col-lg-4">
                        <div class="widget footer-widget">
                            <div class="widget-about">
                                <h3 class="mt-n2">Giving the best uniforms with care</h3>
                                <p class="map-link"><img src="{{ asset('assets/img/icon/map.svg') }}" alt="svg">No.219,
                                    Dr.Radhakrishnan Road, Tatabad, Coimbatore, Tamil Nadu - 641012</p>
                                <div class="sidebar-gallery">
                                    <div class="gallery-thumb">
                                        <img src="{{ asset('assets/img/icon/initiative_soco.png') }}"
                                            alt="Gallery Image">

                                    </div>
                                    <div class="gallery-thumb">
                                        <!-- <img src="{{ asset('assets/img/widget/gal-2-2.jpg') }}" alt="Gallery Image" class="w-100">
                                    <a href="{{ asset('assets/img/widget/gal-2-2.jpg') }}" class="popup-image gal-btn"><i
                                            class="fal fa-plus"></i></a> -->
                                    </div>
                                    <!-- <div class="gallery-thumb">
                                    <img src="{{ asset('assets/img/widget/gal-2-3.jpg') }}" alt="Gallery Image" class="w-100">
                                    <a href="{{ asset('assets/img/widget/gal-2-3.jpg') }}" class="popup-image gal-btn"><i
                                            class="fal fa-plus"></i></a>
                                </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="widget footer-widget">
                            <h3 class="widget_title">Get In Touch</h3>
                            <div>
                                <!-- <p class="footer-text">Monday to Friday: <span class="time">8.30am – 02.00pm</span></p>
                            <p class="footer-text">Saturday, Sunday: <span class="time">Close</span></p> -->
                                <p class="footer-info"><i class="fal fa-envelope"></i>Email: <a
                                        href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>
                                <p class="footer-info"><i class="fas fa-mobile-alt"></i>Phone: <a
                                        href="tel:+919994878486">+91 9994878486</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="widget widget_nav_menu  footer-widget">
                            <h3 class="widget_title">Useful Services</h3>
                            <div class="menu-all-pages-container footer-menu">
                                <ul class="menu">
                                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                                    <li><a href="#">Return/Exchange Policy</a></li>
                                    <li><a href="{{ route('frontend.about-us') }}">About Us</a></li>
                                    <li><a href="#"> Privacy Policy </a></li>
                                    <li><a href="#">Services</a></li>
                                    <li><a href="#">Shipping Policy</a></li>
                                    <li><a href="#">FAQ</a></li>
                                    <li><a href="#">Terms & Conditions</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row flex-row-reverse gy-3 justify-content-between align-items-center">
                    <div class="col-lg-auto">
                        <div class="footer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <p class="copyright-text ">© 2025 SoCo Products Private Limited.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer> <!-- Scroll To Top -->
    <a href="#" class="scrollToTop scroll-btn"><i class="far fa-arrow-up"></i></a>

    <!--********************************
        Code End  Here
******************************** -->

    <!--==============================
    All Js File
============================== -->
    <!-- Jquery -->
    <script src="{{ asset('assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <!-- Slick Slider -->
    <script src="{{ asset('assets/js/slick.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/app.min.js') }}"></script> -->
    <!-- Layerslider -->
    <script src="{{ asset('assets/js/layerslider.utils.js') }}"></script>
    <script src="{{ asset('assets/js/layerslider.transitions.js') }}"></script>
    <script src="{{ asset('assets/js/layerslider.kreaturamedia.jquery.js') }}"></script>
    <!-- jquery ui -->
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Isotope Filter -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>


</body>

</html>
