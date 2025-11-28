@extends('frontend.layouts.app')

@section('content')
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
                <li>
                    <a href="{{ route('frontend.services') }}">Services</a>
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
<!-- Mobile Marquee Auto-Scroll Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupMarquee(wrapperSelector, rowSelector) {
            // Only run on mobile devices (matching CSS breakpoint)
            if (window.innerWidth > 768) return;

            const wrapper = document.querySelector(wrapperSelector);
            const row = document.querySelector(rowSelector);
            
            if (!wrapper || !row) return;

            // Duplicate content for seamless infinite scroll
            const content = row.innerHTML;
            row.innerHTML = content + content; // Duplicate once

            let scrollSpeed = 0.6; // Reduced speed by 40% (was 1)
            let animationId;
            let isTouching = false;
            
            function autoScroll() {
                if (isTouching) return; // Stop loop if touching

                wrapper.scrollLeft += scrollSpeed;
                
                // Infinite scroll logic:
                if (wrapper.scrollLeft >= (wrapper.scrollWidth / 2)) {
                        wrapper.scrollLeft = 0; 
                }
                
                animationId = requestAnimationFrame(autoScroll);
            }

            // Start auto-scroll
            animationId = requestAnimationFrame(autoScroll);

            // Pause on interaction
            wrapper.addEventListener('touchstart', () => { 
                isTouching = true;
                cancelAnimationFrame(animationId); // Stop animation completely
            });
            
            wrapper.addEventListener('touchend', () => { 
                // Resume after a short delay
                setTimeout(() => { 
                    isTouching = false; 
                    animationId = requestAnimationFrame(autoScroll);
                }, 1000); 
            });
            
            // Mouse events for desktop testing
            wrapper.addEventListener('mouseenter', () => { 
                isTouching = true;
                cancelAnimationFrame(animationId);
            });
            
            wrapper.addEventListener('mouseleave', () => { 
                isTouching = false;
                animationId = requestAnimationFrame(autoScroll);
            });
        }

        // Setup for Category and Service marquees
        setupMarquee('.category-marquee-wrapper', '.category_box_row');
        setupMarquee('.service-marquee-wrapper', '.service-marquee-row');
    });
</script>

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
                                <li>
                                    <a href="{{ route('frontend.services') }}">Services</a>
                                </li>

                                </li>
                                <li class="menu-item-has-children">
                                    <!-- <a href="blog.html">Blog</a> -->
                                    <a href="{{ route('frontend.faq') }}">FAQ</a>


                                </li>
                                <li class="menu-item-has-children mega-menu-wrap">
                                    <!-- <a href="#">Pages</a> -->

                                    <a href="{{ route('frontend.contact') }}">Contact Us</a>

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
                        <a href="{{ route('frontend.get-started') }}" class="vs-btn">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
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
        </style>
        
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
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:1296px; height:410px; left:312px; top:213px; background-color:rgb(73 13 89 / 81%); border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:1200px; height:600px; left:350px; top:76px; background-color:rgb(73 13 89 / 81%); border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>
            <div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:1300px; height:700px; left:50%; top:33px; background-color:rgb(73 13 89 / 81%); border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-tablet ls-text-layer" data-ls="static:forever;"></div>
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:284px; left:312px; width:1296px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:361px; left:312px; width:1296px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:100; delayin:300; easingin:easeOutQuint; offsetxout:100; easingout:easeOutQuint;">
                Essenials in One Place
            </h1>
            <p style="font-size:18px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Roboto', sans-serif; color:#ffffff; width:1296px; left:312px; top:438px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetyin:100; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                Shop specific school uniforms and accessories with ease and trust.</p>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:312px; top:494px; width:1296px; font-family:'Poppins', sans-serif; -webkit-background-clip:border-box;"
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
</section>

<!--==============================
Shop by Category Area
==============================-->
<section>
    <div class="category-marquee-wrapper">
        <div class="category_box_row">
            

            <!-- 1. Uniform -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/SchoolUniform.jpeg') }}" alt="Uniform">
                    
                </div>
                <p class="sec-text simple-title">Uniform</p>
            </div>
       
            <!-- 2. Shoes -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/Shoe_school.jpg') }}" alt="Shoes">
                </div>
                <p class="sec-text simple-title">Shoes</p>
            </div>
       
            <!-- 3. Bags -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/SchoolBag_2.jpg') }}" alt="Bags">
                </div>
                <p class="simple-title sec-text">Bags</p>
            </div>
       
            <!-- 4. Stationery -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/Stationery.jpg') }}" alt="Stationery">
                </div>
                <p class="simple-title sec-text">Stationery</p>
            </div>
       
            <!-- 5. Food Container -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/Box_1.jpeg') }}" alt="Food Container">
                </div>
                <p class="simple-title sec-text">Food Container</p>
            </div>
       
            <!-- 6. Drinkware -->
            <div class="simple-cat">
                <div class="simple-box">
                    <img src="{{ asset('assets/img/catagories/Drinkware.jpg') }}" alt="Drinkware">
                </div>
                <p class="simple-title sec-text">Drinkware</p>
            </div>
        </div>
    </div>
       
       
    <style>
        .simple-cat {
            text-align: center;
            margin-bottom: 20px;
        }

        .category_box_row {
            display: flex;
            gap: 30px;
            justify-content: center;
            padding-top: 60px;
            flex-wrap: wrap; /* Allow wrapping */
        }

        .simple-box {
            width: 160px;
            height: 160px;
            border-radius: 18px;
            border: 2px solid #ccc;
            overflow: hidden;
            margin: 0 auto; /* Center in column */
        }

        .simple-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .simple-box:hover img {
            transform: scale(1.15);
        }

        .simple-title {
            margin-top: 8px;
            color: #333;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .category-marquee-wrapper {
                overflow-x: auto;
                width: 100%;
                -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
                scrollbar-width: none; /* Firefox */
            }
            .category-marquee-wrapper::-webkit-scrollbar {
                display: none; /* Chrome/Safari */
            }

            .category_box_row {
                gap: 15px;
                padding-top: 40px;
                flex-wrap: nowrap;
                justify-content: flex-start;
                padding-left: 0;
                padding-right: 0;
                /* animation: scroll 15s linear infinite; REMOVED */
                width: max-content;
                display: flex;
            }
            
            /* Service Section Marquee */
            .service-marquee-wrapper {
                overflow-x: auto;
                width: 100%;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .service-marquee-wrapper::-webkit-scrollbar {
                display: none;
            }

            .service-marquee-row {
                display: flex;
                flex-wrap: nowrap;
                width: max-content;
                /* animation: scroll 15s linear infinite; REMOVED */
            }
            
            .service-marquee-row .service-style1 {
                flex: 0 0 auto;
                width: 280px; /* Fixed width for service cards */
                margin-right: 20px;
                display: flex; /* Enable flex to stretch children */
                height: auto; /* Allow it to grow */
            }
            
            .service-marquee-row .service-body {
                height: 100%; /* Fill the parent height */
                display: flex;
                flex-direction: column;
                justify-content: space-between; /* Push content apart if needed */
                width: 100%;
            }
            
            /* Ensure content takes available space */
            .service-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                align-items: center; /* Center align items horizontally */
                text-align: center; /* Center text */
            }
            
            .service-icon {
                margin: 0 auto 20px auto; /* Ensure icon is centered with margin */
                display: inline-block;
            }
            
            .service-bottom {
                margin-top: auto; /* Push button to bottom */
                width: 100%;
                display: flex;
                justify-content: center;
                padding: 0 15px; /* Add padding to container if needed */
            }
            
            .service-btn {
                width: 100%; /* Full width */
                display: block;
                text-align: center;
            }

            /* @keyframes scroll REMOVED */



            .simple-cat {
                width: 140px;
                flex-shrink: 0;
                margin-right: 15px;
            }

            .simple-box {
                width: 100%;
                height: 140px;
            }
        }



</style>
</section>




<!-- Hover zoom effect -->
<style>
.category-item:hover .category-full-img {
    transform: scale(1.15);
}
</style>


<style>

/* Image default state */
.category-full-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;   /* Smooth zoom animation */
}

/* Zoom-in on hover */
.category-item:hover .category-full-img {
    transform: scale(1.15); /* Adjust zoom level (1.1 to 1.3 recommended) */
}

/* Slight lift effect on the whole circle */


.category-item:hover .category-name {
    color: #490D59;
}

.category-row {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.category-row::-webkit-scrollbar {
    height: 6px;
}

.category-row::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.category-row::-webkit-scrollbar-thumb {
    background: #490D59;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .category-icon-wrapper {
        width: 100px !important;
        height: 100px !important;
    }
    
    .category-icon-wrapper i {
        font-size: 40px !important;
    }
    
    .category-col {
        min-width: 100px !important;
    }
    
    /* Resize Menu Toggle Button */
    .vs-menu-toggle {
        transform: scale(0.8);
        transform-origin: center;
    }
}
</style>

<!--==============================
About Area
==============================-->
<section class=" space-top space-extra-bottom" style="background-color: #ffffff;">
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
                    <div class="col-6 col-md-6">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-2.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">200+</p>
                                <p class="media-title">Satisfied Clients</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-1.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">50+</p>
                                <p class="media-title">Products</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-3.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">24/7</p>
                                <p class="media-title">Support</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-4.svg') }}" alt="icon">
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
        <div class="row vs-carousel d-none d-md-flex" data-slide-show="4" data-ml-slide-show="3" data-lg-slide-show="3"
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
        
        <!-- Mobile Marquee for Services -->
        <div class="service-marquee-wrapper d-md-none">
            <div class="service-marquee-row">
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/services/service.svg') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-1.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Comfort & Care</a></h3>
                            <p class="service-text">Soft, skin-friendly fabrics designed for all-day comfort, ensuring durability, easy care, and lasting freshness.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/services/service (1).svg') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Child Care</a></h3>
                            <p class="service-text">We have a very large indoor space allowing us to have designated areas for different types</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/services/service (2).svg') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-3.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Healthy Meals</a></h3>
                            <p class="service-text">We have a very large indoor space allowing us to have designated areas for different types</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/services/service (3).svg') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Secure Environment</a></h3>
                            <p class="service-text">We have a very large indoor space allowing us to have designated areas for different types</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
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
<section class=" space-top space-extra-bottom" style="background-color: #ffffff;">
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
