@extends('frontend.layouts.app')

@section('content')
<div class="preloader  ">
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
                    <!-- <ul class="sub-menu">
                        <li><a href="class.html">Class Style 1</a></li>
                        <li><a href="class-2.html">Class Style 2</a></li>
                        <li><a href="class-details.html">Class Details</a></li>
                    </ul> -->
                </li>
                <li class="menu-item-has-children">
                    <a href="#">FAQ</a>
                    <!-- <ul class="sub-menu">
                        <li><a href="blog.html">Blog</a></li>
                        <li><a href="blog-details.html">Blog Details</a></li>
                    </ul> -->
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
                                <!-- <span>9994878486</span> -->
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
                            <!-- <li>Welcome to Kiddino Kindergarten & Pre School</li> -->
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
                                    <!-- <a >Home</a> -->
                                    <!-- <ul class="sub-menu">
                                        <li><a href="index.html">Demo Style 1</a></li>
                                        <li><a href="index-2.html">Demo Style 2</a></li>
                                        <li><a href="index-3.html">Demo Style 3</a></li>
                                        <li><a href="index-4.html">Demo Style 4</a></li>
                                        <li><a href="index-5.html">Demo Style 5</a></li>
                                        <li><a href="index-6.html">Demo Style 6</a></li>
                                        <li><a href="index-7.html">Demo Style 7</a></li>
                                        <li><a href="index-8.html">Demo Style 8</a></li>
                                    </ul> -->
                                </li>
                                <li>
                                    <a href="{{ route('frontend.about-us') }}">About Us</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Services</a>

                                </li>
                                <li class="menu-item-has-children">
                                    <!-- <a href="blog.html">Blog</a> -->
                                    <a href="#">FAQ</a>


                                </li>
                                <li class="menu-item-has-children mega-menu-wrap">
                                    <!-- <a href="#">Pages</a> -->

                                    <a href="#">Contact Us</a>

                                </li>
                                <!-- <li>
                                    <a href="contact.html">Contact</a>
                                </li> -->
                            </ul>
                        </nav>
                        <button class="vs-menu-toggle style6 d-inline-block d-lg-none"><i
                                class="fal fa-bars"></i></button>
                    </div>
                    <div class="col-auto  d-none d-lg-block">
                        <div class="header-icons style2">

                        </div>
                    </div>
                    <div class="col-auto d-none d-xl-block">
                        <a href="{{ route('frontend.get-started') }}" class="vs-btn">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<section class="vs-hero-wrapper  ">
    <div class="vs-hero-carousel" data-height="770" data-container="1900" data-slidertype="responsive"
        data-navbuttons="true">

        <!-- Slide 1-->
        <div class="ls-slide" data-ls="duration:12000; transition2d:5;">
            
            <ls-layer
                style="font-size:36px; color:#000; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; border-style:solid; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:255px; height:255px; border-width:60px 60px 60px 60px; border-color:#FFD600; border-radius:50% 50% 50% 50%; top:126px; left:740px; z-index:4; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer" data-ls="static:forever;">
            </ls-layer>
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:711px; height:410px; left:312px; top:213px; background-color:#490D59; border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; opacity:0.9; overflow:visible; width:1200px; height:600px; left:350px; top:76px; background-color:#490D59; border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; opacity:0.9; overflow:visible; width:1300px; height:700px; left:50%; top:33px; background-color:#490D59; border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-tablet ls-text-layer" data-ls="static:forever;"></div>
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:284px; left:312px; width:711px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:361px; left:312px; width:711px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:100; delayin:300; easingin:easeOutQuint; offsetxout:100; easingout:easeOutQuint;">
                Essenials in One Place
            </h1>
            <p style="font-size:18px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Roboto', sans-serif; color:#ffffff; width:711px; left:312px; top:438px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetyin:100; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                Shop specific school uniforms and accessories with ease and trust.</p>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:100%; top:494px; font-family:'Poppins', sans-serif; width:711px; margin-left:-877px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-html-layer"
                data-ls="offsetyin:100; delayin:700; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                <a href="{{ route('frontend.get-started') }}" class="vs-btn">Shop Now</a>
            </div>
            <h1 style="font-size:90px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:90px; color:#ffffff; top:141px; left:50%; width:1200px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:90px; color:#ffffff; top:255px; left:50%; width:1200px; font-size:90px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetxin:100; delayin:400; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                Essenials in One Place
            </h1>
            <p style="stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Roboto', sans-serif; color:#ffffff; width:1200px; left:50%; top:384px; font-size:38px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetyin:100; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                Shop specific school uniforms and accessories with ease and trust.</p>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:50%; top:495px; font-family:'Poppins', sans-serif; width:1200px; margin-left:0px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-html-layer"
                data-ls="offsetyin:100; delayin:700; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                <a href="{{ route('frontend.get-started') }}" class="vs-btn">Shop Now</a>
            </div>
            <h1 style="font-size:110px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:110px; color:#ffffff; top:113px; left:50%; width:1200px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-tablet ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:110px; color:#ffffff; top:247px; left:50%; width:1200px; font-size:110px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-tablet ls-text-layer"
                data-ls="offsetxin:100; delayin:400; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                Essentials in One Place
            </h1>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:50%; top:430px; font-family:'Poppins', sans-serif; width:1200px; margin-left:0px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-tablet ls-html-layer"
                data-ls="offsetyin:100; delayin:700; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                <a href="{{ route('frontend.get-started') }}" class="vs-btn">Shop Now</a>
            </div>
        </div>



    </div>
</section><!--==============================
About Area
==============================-->
<section class=" space-top space-extra-bottom">
    <div class="container">
        <div class="row gx-70 align-items-center">

            <div class="col-lg-6">
                <div class="img-box1">
                    <div class="vs-circle"></div>
                    <div class="img-1 mega-hover"><img src="{{ asset('assets/img/about/about_1.svg') }}" alt="about">
                    </div>
                    <div class="img-2 mega-hover"><img src="{{ asset('assets/img/about/about_2.svg') }}" alt="about">
                    </div>
                    <div class="img-3 mega-hover"><img src="{{ asset('assets/img/about/about_3.svg') }}" alt="about">
                    </div>
                    <div class="img-4 mega-hover"><img src="{{ asset('assets/img/about/about_4.svg') }}" alt="about">
                    </div>
                </div>
            </div>

            <div class="col-lg-6 text-center text-lg-start">
                <span class="sec-subtitle">7+ years, trusted quality,</span>
                <h2 class="sec-title">The Easiest Way to Buy School Uniforms</h2>
                <p class="sec-text pe-xl-5 mb-4 pb-xl-3">We make school uniform shopping easy and hassle-free. As a
                    trusted uniform
                    manufacturer, we are now bringing our expertise online. With a commitment
                    to quality, timely delivery, and customer satisfaction, we ensure every uniform
                    meets the highest standards.</p>
                <div class="row gx-70 justify-content-center justify-content-lg-start text-md-start">
                    <div class="col-auto">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-2.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">200+</p>
                                <p class="media-title">Satisfied Clients</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-1.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">50+</p>
                                <p class="media-title">Products</p>
                            </div>
                        </div>
                    </div>


                    <div class="col-auto">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-2.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">24/7</p>
                                <p class="media-title">Support</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-2.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">7+</p>
                                <p class="media-title">Years</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!--==============================
Service Area
==============================-->
<section class=" space-extra-bottom">
    <div class="container">
        <div class="title-area text-center">
            <div class="sec-bubble">
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
            </div>
            <h2 class="sec-title">Smart, Comfortable & Built to Last.</h2>
            <p class="sec-text">From classrooms to playgrounds, SOCO uniforms offer exceptional comfort, style, and
                durability — trusted by schools across India.</p>
        </div>
        <div class="row vs-carousel" data-slide-show="4" data-ml-slide-show="3" data-lg-slide-show="3"
            data-md-slide-show="2">
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/services/service.svg') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-1.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Comfort & Care</a></h3>
                        <p class="service-text">Soft, skin-friendly fabrics designed
                            for all-day comfort, ensuring durability,
                            easy care, and lasting freshness.</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/services/service (1).svg') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Child Care</a></h3>
                        <p class="service-text">We have a very large indoor space allowing us to have designated
                            areas for different types</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/services/service (2).svg') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-3.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Healthy Meals</a></h3>
                        <p class="service-text">We have a very large indoor space allowing us to have designated
                            areas for different types</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/services/service (3).svg') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Secure Environment</a></h3>
                        <p class="service-text">We have a very large indoor space allowing us to have designated
                            areas for different types</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div data-bg-src="{{ asset('assets/img/bg/bg-h-1-1.jpg') }}">
</div>

</div>
<section class=" space-extra-bottom">
    <div class="container">
        <div class="row gx-80">

            <div class="col-lg-12 align-self-center">
                <div class="title-area text-center text-lg-start">
                    <span class="sec-subtitle">Clear Your Doubts</span>
                    <h2 class="sec-title">Frequently Asked Questions</h2>
                </div>
                <div class="accordion accordion-style1 faq-two-column" id="faqVersion1">
                    <div class="accordion-item active">
                        <div class="accordion-header" id="headingOne1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
                                How can I contact customer support?
                            </button>
                        </div>
                        <div id="collapseOne1" class="accordion-collapse collapse show"
                            aria-labelledby="headingOne1" data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>You can reach us via email at hello@theskoolstore.com or call us at +91
                                    9994878486. Our support team is happy to assist you!</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingTwo1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
                                What payment methods do you accept?
                            </button>
                        </div>
                        <div id="collapseTwo1" class="accordion-collapse collapse" aria-labelledby="headingTwo1"
                            data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>We accept online payments via credit/debit cards, UPI, net banking, and other
                                    secure payment options. </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingThree1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree1" aria-expanded="false"
                                aria-controls="collapseThree1">
                                How do I choose the correct size?
                            </button>
                        </div>
                        <div id="collapseThree1" class="accordion-collapse collapse" aria-labelledby="headingThree1"
                            data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>
                                    Each product page includes a size chart to help you select the perfect fit. If
                                    you're unsure, refer to the explanation video provided for each garment.

                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingFour1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour1" aria-expanded="false" aria-controls="collapseFour1">
                                What if my school is not listed on the website?
                            </button>
                        </div>
                        <div id="collapseFour1" class="accordion-collapse collapse" aria-labelledby="headingFour1"
                            data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>
                                    We currently have uniforms available only for listed schools. However, we are
                                    continuously expanding our collection, so stay tuned!
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingFive1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive1" aria-expanded="false" aria-controls="collapseFive1">
                                How do I know if my order was placed successfully?
                            </button>
                        </div>
                        <div id="collapseFive1" class="accordion-collapse collapse" aria-labelledby="headingFive1"
                            data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>Enrolment Events are like open days or open weeks at Busy Bees. It's a chance for
                                    you to visit your local nursery, take a look around, and see some of exciting
                                    activities in action. </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingSix1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSix1" aria-expanded="false" aria-controls="collapseSix1">
                                How do I find my school's uniform?
                            </button>
                        </div>
                        <div id="collapseSix1" class="accordion-collapse collapse" aria-labelledby="headingSix1"
                            data-bs-parent="#faqVersion1">
                            <div class="accordion-body">
                                <p>Simply enter your school name in the search bar, and you'll see the authorized
                                    and optional products available.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection
