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

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
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
        
        .vs-hero-wrapper::after {
            position: absolute;
            left: 0;
            width: 100%;
            content: "";
            background: rgba(0, 0, 0, .50);
            height: 100%;
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
             .bottom-text {
            font-size: 22px !important;
            }
        }
        /* Control SOCO logo image size in footer - applies to all screen sizes */
        .sidebar-gallery .gallery-thumb img {
            width: 250px !important;
            height: auto !important;
            max-width: 130px;
        }
        
        /* Initiative wrapper styling */
        .initiative-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        /* Row for: [An Initiative of] [SO/CO logo] */
        .initiative-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .initiative-text {
            color: #ffffff;
            font-size: 16px;
            font-weight: 400;
            margin: 0;
        }
        
        .initiative-tagline {
            color: #ffffff;
            font-size: 8px;
            font-weight: 400;
            /* font-style: italic; */
            font-family: 'Playfair Display', serif;
            margin: 0;
            margin-left: 130px; /* roughly under the logo */
        }
        
        /* Footer heading font size */
       .bottom-text {
            font-size: 30px;
            }

        /* Mobile view - Make SoCo logo smaller and center it */
        @media (max-width: 768px) {
            /* Center and resize main footer logo */
            .footer-top .col-lg {
                text-align: center !important;
                display: flex;
                justify-content: center;
            }
            
            .footer-top .col-lg img {
                max-width: 150px !important;
                height: auto !important;
            }
            
            /* Center and resize SoCo initiative logo */
            .sidebar-gallery .gallery-thumb {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .sidebar-gallery .gallery-thumb img {
                width: 150px !important;
                height: auto !important;
                max-width: 100%;
                margin: 0 auto;
            }
            
            /* Center initiative wrapper on mobile */
            .initiative-wrapper {
                align-items: center !important;
                text-align: center;
            }
            
            .initiative-text,
            .initiative-tagline {
                text-align: center;
            }

            .initiative-tagline {
                /* margin-left: 0 !important; */
            }
            
            /* Center the logo container on mobile */
            .widget-about {
                text-align: center;
            }
        }

        /* Fix footer menu spacing - remove extra space after FAQ */
        .footer-widget.widget_nav_menu {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        
        .footer-widget.widget_nav_menu ul,
        .footer-widget.widget_nav_menu .footer-menu ul,
        .footer-widget.widget_nav_menu .footer-menu ul.menu {
            padding: 0 !important;
            margin: 0 !important;
            margin-bottom: 0 !important;
            gap: 0 !important;
            row-gap: 0 !important;
        }
        
        /* Set consistent margin-bottom for ALL footer menu links and list items */
        .footer-widget.widget_nav_menu .footer-menu ul.menu li,
        .footer-widget.widget_nav_menu .footer-menu ul.menu li a,
        .footer-widget.widget_nav_menu ul li,
        .footer-widget.widget_nav_menu ul li a {
            margin-bottom: 18px !important;
            margin-top: 0 !important;
            padding-bottom: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Ensure last items also have consistent margin */
        .footer-widget.widget_nav_menu .footer-menu ul.menu li:nth-last-child(-n+2),
        .footer-widget.widget_nav_menu .footer-menu ul.menu li:nth-last-child(-n+2) a {
            margin-bottom: 36px !important;
            padding-bottom: 0 !important;
        }
        
        /* Remove any padding/margin from the menu container */
        .footer-widget .footer-menu,
        .footer-widget .menu-all-pages-container {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Override ALL widget_nav_menu links in footer - most specific */
        .footer-widget.widget_nav_menu a,
        .footer-widget.widget_nav_menu .footer-menu a,
        .footer-widget.widget_nav_menu .footer-menu ul.menu a {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        
        /* Ensure no spacing on the widget itself */
        .footer-widget.widget_nav_menu .widget {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
       
        
        
        /* Remove any bottom spacing from the last row of the grid */
        .footer-widget.widget_nav_menu .footer-menu ul.menu > li:nth-last-child(-n+2) {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        
       
        
        /* Ensure widget title doesn't create extra space below */
        .footer-widget.widget_nav_menu .widget_title {
            margin-bottom: 20px !important;
        }
        
        /* Final comprehensive override - remove ALL spacing from footer menu */
        .footer-widget.widget_nav_menu .menu-all-pages-container.footer-menu ul.menu {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .footer-widget.widget_nav_menu .menu-all-pages-container.footer-menu ul.menu li:last-child,
        .footer-widget.widget_nav_menu .menu-all-pages-container.footer-menu ul.menu li:nth-last-child(2) {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        /* Footer background color */
        .footer-wrapper.footer-layout1 {
            background-color: #071B54 !important;
        }

        /* Footer logo sizing - responsive */
        .footer-top .col-lg img[alt="logo"] {
            width: 160px;
            height: auto;
        }

        /* Tablet: 769px–991px (slightly larger, but not huge) */
        @media (min-width: 769px) and (max-width: 991px) {
            .footer-top .col-lg img[alt="logo"] {
                width: 200px;
            }
            
            /* Center footer content on tablet */
            .footer-top .row {
                justify-content: center !important;
                text-align: center !important;
            }
            
            .footer-top .col-lg,
            .footer-top .col-lg-auto {
                text-align: center !important;
                margin-bottom: 15px;
            }
            
            .widget-area .row {
                justify-content: center !important;
                text-align: center !important;
            }
            
            .widget-area .col-lg-4,
            .widget-area .col-md-6 {
                text-align: center !important;
            }
            
            .footer-widget {
                text-align: center !important;
            }
            
            .widget-about {
                text-align: center !important;
            }
            
            .map-link {
                justify-content: center !important;
            }
            
            .footer-menu ul.menu {
                text-align: center !important;
            }
            
            .footer-menu ul.menu li {
                display: inline-block;
                margin: 0 10px;
            }
            
            /* Center initiative wrapper on tablet */
            .initiative-wrapper {
                align-items: center !important;
                text-align: center;
            }
            
            .initiative-text,
            .initiative-tagline {
                text-align: center;
            }

            .initiative-tagline {
                margin-left: 0 !important;
            }
            
            .sidebar-gallery .gallery-thumb img {
                width: 200px !important;
            }
        }

        /* Desktop: 992px–1299px */
        @media (min-width: 992px) and (max-width: 1299px) {
            .footer-top .col-lg img[alt="logo"] {
                width: 240px;
            }
            
            /* Center map-link on desktop */
            .map-link {
                justify-content: center !important;
            }
        }
        
        /* Center map-link for all screen sizes */
        .map-link {
            justify-content: center !important;
            /* margin-left: auto !important; */
            margin-right: auto !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px;
            text-align: left !important;
        }

        /* Make map icon match the yellow circular style of other footer icons */
        .map-link i {
            width: var(--icon-size, 85px);
            height: var(--icon-size, 42px);
            line-height: var(--icon-size, 42px);
            text-align: center;
            border-radius: 50%;
            background-color: var(--theme-color2);
            color: var(--black-color);
            font-size: 18px;
        }
        
        /* Ensure parent container centers the map-link */
        .widget-about {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        
        /* Center map-link on all desktop sizes */
        @media (min-width: 1300px) {
            .map-link {
                justify-content: center !important;
                /* margin-left: auto !important; */
                margin-right: auto !important;
            }
        }

        /* Large desktop: 1300px and above */
        @media (min-width: 1300px) {
            .footer-top .col-lg img[alt="logo"] {
                width: 220px;
            }
        }

        /* Fix address text wrapping - align wrapped lines with text, not icon */
        .footer-info-address {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .footer-info-address i {
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .footer-info-text {
            flex: 1;
            min-width: 0;
        }
        
        .footer-info-text a {
            display: inline;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

    </style>

</head>

<body>
    @yield('content')



    @unless(View::hasSection('no_footer'))
    <footer class="footer-wrapper footer-layout1" data-bg-src="{{ asset('assets/img/bg/footer-bg-1-1.png') }}">
        <div class="footer-top">
            <div class="container">
                <div class="row gx-60 gy-4 text-center text-lg-start justify-content-between align-items-center">
                     <div class="col-lg"><a href="{{ route('frontend.index') }}"><img
                                 src="{{ asset('assets/img/soco_logo/logo_voilet_v.png') }}" alt="logo"></a></div>
                    <div class="col-lg-auto">
                        <h3 class="h5 mb-0 text-white">
                                Shop Your School’s Uniforms & Essentials in one place.</h3>
                    </div>
                    <div class="col-lg-auto"><a href="{{ route('login') }}" class="vs-btn">Shop Now</a></div>
                </div>
            </div>
        </div>
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-center gx-60">
                    <div class="col-lg-4">
                        <div class="widget footer-widget">
                            <div class="widget-about">
                                <h3 class="mt-n2 bottom-text">Giving the best uniforms with care</h3>
                                
                                <div class="sidebar-gallery">
                                    <div class="initiative-wrapper">
                                        <div class="initiative-row">
                                            <p class="initiative-text">An Initiative of</p>
                                            <div class="gallery-thumb">
                                                <img src="{{ asset('assets/img/soco_logo/logo_r_soco.png') }}"
                                                    alt="SOCO Logo">
                                            </div>
                                        </div>
                                        <p class="initiative-tagline">Create Your own Identity</p>
                                    </div>
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
                                <p class="footer-info">
                                    <i class="fal fa-envelope"></i>
                                    Email:
                                    <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a>
                                </p>

                                <p class="footer-info">
                                    <i class="fas fa-mobile-alt"></i>
                                    Phone:
                                    <a href="tel:+919994878486">+91 99948 78486</a>
                                </p>

                                <p class="footer-info footer-info-address">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="footer-info-text">
                                        <a href="https://www.google.com/maps/dir//219,+Dr+Radhakrishna+St+near+Indian+Bank+Tatabad,+Sivananda+Colony,+Tatabad+Gandhipuram,+Coimbatore,+Tamil+Nadu+641012/@11.0220074,76.9585226,16z/data=!4m5!4m4!1m0!1m2!1m1!1s0x3ba858fa2a8132a5:0x8022e83379efd97a"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                            No. 219, Dr. Radhakrishnan Road,
                                             Tatabad,
                                            Coimbatore, Tamil Nadu – 641012,
                                            India
                                        </a>
                                    </span>
                                </p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="widget widget_nav_menu  footer-widget">
                            <h3 class="widget_title">Useful Services</h3>
                            <div class="menu-all-pages-container footer-menu">
                                <ul class="menu">
                                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                                    <li><a href="{{ route('frontend.return-exchange') }}">Exchange Policy</a></li>
                                    <li><a href="{{ route('frontend.about-us') }}">About Us</a></li>
                                    <li><a href="{{ route('frontend.privacy-policy') }}">Privacy Policy</a></li>
                                    <li><a href="{{ route('frontend.services') }}">Services</a></li>
                                    <li><a href="{{ route('frontend.shipping-policy') }}">Shipping Policy</a></li>
                                    <li><a href="{{ route('frontend.faq') }}">FAQ</a></li>
                                    <li><a href="{{ route('frontend.terms-conditions') }}">Terms & Conditions</a></li>
                                    <li><a href="{{ route('frontend.contact') }}">Contact Us</a></li>
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
                            <a href="https://www.facebook.com/profile.php?id=6156745997746" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://x.com/SoCoproducts" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.instagram.com/socoproducts/" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.linkedin.com/company/soco-products-privatelimited/?viewAsMember=true" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://www.youtube.com/@Socous" target="_blank"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <p class="copyright-text ">© 2025 SoCo Products Private Limited.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    @endunless <!-- Scroll To Top -->
    <a href="#" class="scrollToTop scroll-btn"><i class="far fa-arrow-up"></i></a>

    <!--********************************
        Code End  Here
******************************** -->

    <!--==============================
    All Js File
============================== -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Isotope Filter -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
      AOS.init();
    </script>


</body>

</html>
