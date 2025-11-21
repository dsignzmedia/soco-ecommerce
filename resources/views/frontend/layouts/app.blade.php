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
                    <div class="col-lg-auto"><a href="#" class="vs-btn">Shop Now</a></div>
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
                                    <li><a href="#">About Us</a></li>
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
