@extends('frontend.layouts.app')

@section('content')

@include('frontend.partials.header')


<section class="vs-hero-wrapper  ">
    <div class="vs-hero-carousel" data-height="770" data-container="1900" data-slidertype="responsive"
        data-navbuttons="true">

        <!-- Slide 1-->
        <div class="ls-slide" data-ls="duration:12000; transition2d:5;">
            
            <!--<ls-layer-->
            <!--    style="font-size:36px; color:#000; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; border-style:solid; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:255px; height:255px; border-width:60px 60px 60px 60px; border-color:#FFD600; border-radius:50% 50% 50% 50%; top:126px; left:740px; z-index:4; -webkit-background-clip:border-box;"-->
            <!--    class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer" data-ls="static:forever;">-->
            <!--</ls-layer>-->
            <!--<div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:900px; height:410px; left:500px; top:213px; background-color:rgb(73 13 89 / 81%); border-radius:100px; z-index:5; -webkit-background-clip:border-box;"-->
            <!--    class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>-->
            <!--<div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:1200px; height:600px; left:350px; top:76px; background-color:rgb(73 13 89 / 81%); border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"-->
            <!--    class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer" data-ls="static:forever;"></div>-->
            <!--<div style="font-size:36px; stroke:#000; stroke-width:0px; text-align:left; font-style:normal; text-decoration:none; text-transform:none; font-weight:425; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; width:1600px; height:600px; left:50%; top:33px; background-color:rgb(73 13 89 / 81%); border-radius:213px 206px 50px 213px; z-index:5; -webkit-background-clip:border-box;"-->
            <!--    class="ls-l ls-hide-desktop ls-hide-tablet ls-text-layer" data-ls="static:forever;"></div>-->
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:284px; left:312px; width:1296px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="font-size:60px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:60px; color:#ffffff; top:361px; left:312px; width:1296px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetxin:100; delayin:300; easingin:easeOutQuint; offsetxout:100; easingout:easeOutQuint;">
                Essentials in One Place
            </h1>
            <p style="font-size:18px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Roboto', sans-serif; color:#ffffff; width:1296px; left:312px; top:438px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-text-layer"
                data-ls="offsetyin:100; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
               Effortless School Shopping- One Place for All Student Needs</p>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:312px; top:494px; width:1296px; font-family:'Poppins', sans-serif; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-tablet ls-hide-phone ls-html-layer"
                data-ls="offsetyin:100; delayin:700; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                <a href="{{ route('login') }}" class="vs-btn vs-btn-xl">Shop Now</a>
            </div>
            <h1 style="font-size:90px; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:90px; color:#ffffff; top:141px; left:50%; width:1200px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetxin:-100; delayin:200; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                All Your School
            </h1>
            <h1 style="stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:600; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Poppins', sans-serif; line-height:90px; color:#ffffff; top:255px; left:50%; width:1200px; font-size:90px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetxin:100; delayin:400; easingin:easeOutQuint; offsetxout:-100; easingout:easeOutQuint;">
                Essentials in One Place
            </h1>
            <p style="stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; font-family:'Roboto', sans-serif; color:#ffffff; width:1200px; left:50%; top:384px; font-size:38px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-text-layer"
                data-ls="offsetyin:100; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                Effortless School Shopping- One Place for All Student Needs</p>
            <div style="font-size:30px; color:#000; stroke:#000; stroke-width:0px; text-align:center; font-style:normal; text-decoration:none; text-transform:none; font-weight:400; letter-spacing:0px; background-position:0% 0%; background-repeat:no-repeat; background-clip:border-box; overflow:visible; left:50%; top:495px; font-family:'Poppins', sans-serif; width:1200px; margin-left:0px; -webkit-background-clip:border-box;"
                class="ls-l ls-hide-desktop ls-hide-phone ls-html-layer"
                data-ls="offsetyin:100; delayin:700; easingin:easeOutQuint; offsetyout:100; easingout:easeOutQuint;">
                <a href="{{ route('login') }}" class="vs-btn vs-btn-xl">Shop Now</a>
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
                <a href="{{ route('login') }}" class="vs-btn vs-btn-xl">Shop Now</a>
            </div>
        </div>



    </div>
</section>

<!--==============================
Shop by Category Area
==============================-->
<section>

       
       
    <style>
        .simple-cat {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Desktop and Tablet: enable horizontal scroll when content exceeds */
        .category-marquee-wrapper {
            overflow-x: hidden; /* Hide scrollbar but allow JavaScript transform */
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Hide scrollbar completely */
        .category-marquee-wrapper::-webkit-scrollbar {
            display: none;
        }
        
        .category-marquee-wrapper {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        /* Hide content initially to prevent flash, show after JS determines layout */
        .category-marquee-wrapper:not(.layout-ready) {
            opacity: 0;
            visibility: hidden;
        }
        
        .category-marquee-wrapper.layout-ready {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s ease-in;
        }
        
        /* Default: Center content to prevent flash (JS will change if content overflows) */
        .category-marquee-wrapper .category_box_row {
            justify-content: center !important;
        }
        
        /* Override when content overflows */
        .category-marquee-wrapper.content-overflows .category_box_row {
            justify-content: flex-start !important; /* Align left when content overflows */
        }
        
        /* When content fits screen - center it (explicit) */
        .category-marquee-wrapper.content-fits-screen {
            overflow-x: visible;
        }
        
        .category-marquee-wrapper.content-fits-screen .category_box_row {
            justify-content: center;
            transform: none !important;
            width: 100%;
            padding-left: 20px;
            padding-right: 20px;
        }

        /* When content overflows - enable scrolling and align left */
        .category-marquee-wrapper.content-overflows {
            overflow-x: hidden;
            overflow-y: hidden;
        }
        
        .category-marquee-wrapper.content-overflows .category_box_row {
            justify-content: flex-start; /* Align left when content overflows */
        }
        
        .category-marquee-wrapper.content-overflows::-webkit-scrollbar {
            height: 6px;
        }
        
        .category-marquee-wrapper.content-overflows::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .category-marquee-wrapper.content-overflows::-webkit-scrollbar-thumb {
            background: #490D59;
            border-radius: 10px;
        }

        .category_box_row {
            display: flex;
            gap: 30px;
            justify-content: center; /* Default to center, JS will change if content overflows */
            padding-top: 60px;
            padding-left: 20px;
            padding-right: 20px;
            flex-wrap: nowrap; /* Keep in single row */
            width: max-content; /* Allow content to exceed container */
            /* Ensure row can be scrolled natively when needed */
            position: relative;
<<<<<<< HEAD
        }
        
        /* When content overflows, change to flex-start */
        .category-marquee-wrapper.content-overflows .category_box_row {
            justify-content: flex-start;
=======
>>>>>>> 8da25f1e54eaa07215c57ed1e8f159850574279a
        }
        
        /* When content overflows, change to flex-start */
        .category-marquee-wrapper.content-overflows .category_box_row {
            justify-content: flex-start;
        }

        /* Tablet/Medium screens: adjust layout */
        @media (min-width: 769px) and (max-width: 1116px) {
            .category_box_row {
                gap: 20px;
            }
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
                overflow-x: hidden;
                width: 100%;
            }

            .category_box_row {
                gap: 15px;
                padding-top: 40px;
                flex-wrap: nowrap;
                justify-content: center; /* Default to center on mobile too */
                padding-left: 0;
                padding-right: 0;
                /* animation: scroll 15s linear infinite; REMOVED */
                width: max-content;
                will-change: transform; 
                display: flex;
                align-items: flex-start;
            }
            
            /* When content overflows on mobile, change to flex-start */
            .category-marquee-wrapper.content-overflows .category_box_row {
                justify-content: flex-start;
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
    .space-top-mobile {
        padding-top: 2px !important;
    }
}

/* Fix for Service Card Height Consistency */
.service-style1 .service-img {
    height: 350px; /* Fixed height for all service images */
    width: 100%;
    overflow: hidden;
}

.service-style1 .service-img img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensure image covers the area without distortion */
}
</style>

<!--==============================
Featured Products Area
==============================-->
<style>
/* Critical CSS - prevents flash, loads before content */
.category-marquee-wrapper:not(.layout-ready) {
    opacity: 0 !important;
    visibility: hidden !important;
}
.category-marquee-wrapper.layout-ready {
    opacity: 1 !important;
    visibility: visible !important;
    transition: opacity 0.15s ease-in;
}
.category-marquee-wrapper .category_box_row {
    justify-content: center !important;
}
.category-marquee-wrapper.content-overflows .category_box_row {
    justify-content: flex-start !important;
}
</style>
@if(isset($publicProducts) && $publicProducts->count() > 0)
<section style="background-color:#ffffff;">
    <div class="category-marquee-wrapper">
        <div class="category_box_row" style="justify-content: center !important;">

            @foreach($publicProducts as $product)
                <a href="{{ route('frontend.shop.detail', $product->id) }}" class="simple-cat">

                    <div class="simple-box">
                        @if($product->featured_image)
                            <img 
                                src="{{ Str::startsWith($product->featured_image, 'http') 
                                    ? $product->featured_image 
                                    : asset('storage/' . $product->featured_image) }}" 
                                alt="{{ $product->product_name }}"
                                onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';"
                            >
                        @else
                            <img 
                                src="{{ asset('assets/img/no image/no_image.png') }}" 
                                alt="{{ $product->product_name }}"
                            >
                        @endif
                    </div>

                    <p class="sec-text simple-title">
                        {{ $product->product_name }}
                    </p>

                </a>
            @endforeach

        </div>
    </div>
</section>
@endif


<!--==============================
About Area
==============================--> 
<section class=" space-top space-extra-bottom space-top-mobile" style="background-color: #ffffff;">
    <div class="container">
        <div class="row gx-70 align-items-center">

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                <div class="img-box1">
                    <div class="vs-circle"></div>
                    <div class="img-1 mega-hover"><img src="{{ asset('assets/img/indian_faces/about_1.jpeg') }}" alt="about">
                    </div>
                    <div class="img-2 mega-hover"><img src="{{ asset('assets/img/indian_faces/about_2.png') }}" alt="about">
                    </div>
                    <div class="img-3 mega-hover"><img src="{{ asset('assets/img/about/about_3.svg') }}" alt="about">
                    </div>
                    <div class="img-4 mega-hover"><img src="{{ asset('assets/img/about/about_4.svg') }}" alt="about">
                    </div>
                </div>`
            </div>

            <div class="col-lg-6 text-center text-lg-start">
                <span class="sec-subtitle">7+ years, trusted quality,</span>
                <h2 class="sec-title">Your Easy, All in one destination for school Uniform and Essentials.</h2>
                <p class="sec-text pe-xl-5 mb-4 pb-xl-3">We make school uniform shopping easy and hassle-free. As a
                    trusted uniform
                    manufacturer, we are now bringing our expertise online. With a commitment
                    to quality, timely delivery, and customer satisfaction, we ensure every uniform
                    meets the highest standards.</p>
                <div class="row gx-70 justify-content-center justify-content-lg-start text-md-start" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
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

                    <div class="col-6 col-md-6" >
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/ab-1-1.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">50+</p>
                                <p class="media-title">Products</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6" >
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/coun-1-3.svg') }}" alt="icon">
                            </div>
                            <div class="media-body">
                                <p class="media-label">24/7</p>
                                <p class="media-title">Support</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6" >
                        <div class="vs-media media-style1">
                            <div class="media-icon"><img src="{{ asset('assets/img/icon/coun-1-4.svg') }}" alt="icon">
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
</section>
<!--==============================
Service Area
==============================-->
<section class=" space-extra-bottom">
    <div class="container">
        <div class="title-area text-center" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
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
            data-md-slide-show="2" data-autoplay="true">
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/indian_faces/service_1.png') }}" alt="service"></a></div>
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
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/indian_faces/service_5.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">On Time Delivery</a></h3>
                        <p class="service-text">Reliable, punctual delivery—your school essentials arrive exactly when you need them</p>
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
                        <h3 class="service-title"><a href="#">Student Delight</a></h3>
                        <p class="service-text">Designed to make every student feel confident and happy.creating a delightful experience from classroom to playground.</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img
                                src="{{ asset('assets/img/indian_faces/service_4.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Wide Range</a></h3>
                        <p class="service-text">A complete collection under one roof. Explore a wide range tailored to meet every school and student’s needs.</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
                                src="{{ asset('assets/img/indian_faces/service_2.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/check-list.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="#">Transparency</a></h3>
                        <p class="service-text">Clear, honest, and straightforward at every step. From pricing to product quality, we ensure complete visibility</p>
                        <div class="service-bottom">
                            <a href="#" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Horizontal Slider for Services -->
        <div class="service-marquee-wrapper d-md-none">
            <div class="service-marquee-row">
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/indian_faces/service_1.png') }}" alt="service"></a></div>
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
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/indian_faces/service_5.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">On Time Delivery</a></h3>
                            <p class="service-text">Reliable, punctual delivery—your school essentials arrive exactly when you need them</p>
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
                            <h3 class="service-title"><a href="#">Student Delight</a></h3>
                            <p class="service-text">Designed to make every student feel confident and happy.creating a delightful experience from classroom to playground.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img src="{{ asset('assets/img/indian_faces/service_4.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Wide Range</a></h3>
                            <p class="service-text">A complete collection under one roof. Explore a wide range tailored to meet every school and student’s needs.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_2.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/check-list.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="#">Transparency</a></h3>
                            <p class="service-text">Clear, honest, and straightforward at every step. From pricing to product quality, we ensure complete visibility</p>
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

<div data-bg-src="{{ asset('assets/img/bg/bg-h-1-1.jpg') }}">
</div>

</div>
<!--==============================
Process Area
==============================-->
<section class="space-top space-extra-bottom" style="background-color: #ffffff;">
   
    <div class="container">
        <div class="title-area text-center" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
            <div class="sec-bubble">
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
            </div>
            <h2 class="sec-title">How To Buy Product</h2>
            <span class="sec-subtitle">SIMPLE STEPS TO PURCHASE</span>
        </div>
        <div class="row gy-4 position-relative" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
            <div class="process-center-circle d-none d-lg-block"></div>
            <!-- Step 1 -->
            <div class="col-lg-6 col-xl-6">
                <div class="process-box" style="background-color: #8BC34A;">
                    <div class="process-img">
                         <img src="{{ asset('assets/gif/soco%20gifs/step%201.gif') }}" alt="Step 1">
                    </div>
                    <div class="process-content">
                        <span class="process-step">Step 1</span>
                        <h3 class="process-title">Enter Your Details</h3>
                        <p class="process-text">Provide your information to quickly locate your school and its specific products</p>
                    </div>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="col-lg-6 col-xl-6">
                <div class="process-box" style="background-color: #00BCD4;">
                    <div class="process-img">
                         <img src="{{ asset('assets/gif/soco%20gifs/step%202.gif') }}" alt="Step 2">
                    </div>
                    <div class="process-content">
                        <span class="process-step">Step 2</span>
                        <h3 class="process-title">Select Your Product</h3>
                        <p class="process-text">Choose the required items and add them to your cart</p>
                    </div>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="col-lg-6 col-xl-6">
                <div class="process-box" style="background-color: #512DA8;">
                    <div class="process-img">
                         <img src="{{ asset('assets/gif/soco%20gifs/step%203.gif') }}" alt="Step 3">
                    </div>
                    <div class="process-content">
                        <span class="process-step">Step 3</span>
                        <h3 class="process-title">Review & Checkout</h3>
                        <p class="process-text">Verify your items and proceed to check out</p>
                    </div>
                </div>
            </div>
            <!-- Step 4 -->
            <div class="col-lg-6 col-xl-6">
                <div class="process-box" style="background-color: #E64A19;">
                    <div class="process-img">
                         <img src="{{ asset('assets/gif/soco%20gifs/step%204.gif') }}" alt="Step 4">
                    </div>
                    <div class="process-content">
                        <span class="process-step">Step 4</span>
                        <h3 class="process-title">Get it Delivered</h3>
                        <p class="process-text">Receive your products at your doorstep with timely delivery</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .process-box {
            display: flex;
            align-items: center;
            padding: 30px 40px;
            border-radius: 20px;
            color: #fff;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .process-center-circle {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            border: 50px solid var(--theme-color2, #fec624);
            border-radius: 50%;
            z-index: 0;
            background-color: transparent;
        }
        .process-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        .process-img {
            flex: 0 0 130px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: #fefcfb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 30px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .process-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .process-content {
            flex: 1;
        }
        .process-step {
            display: inline-block;
            background: #fff;
            color: #222;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .process-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #fff;
        }
        .process-text {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        /* Mobile Responsive */
        @media (max-width: 767px) {
            .process-box {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }
            .process-img {
                margin-right: 0;
                margin-bottom: 20px;
                width: 120px;
                height: 120px;
            }
            .process-title {
                font-size: 1.4rem;
            }
        }
    </style>
</section>

<section class=" space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row gx-80">

            <div class="col-lg-12 align-self-center">
                <div class="title-area text-center text-lg-start" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                    <span class="sec-subtitle">Clear Your Doubts</span>
                    <h2 class="sec-title">Frequently Asked Questions</h2>
                </div>
                <div class="accordion accordion-style1 faq-two-column" id="faqVersion1" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingOne1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne1" aria-expanded="false" aria-controls="collapseOne1">
                                How can I contact customer support?
                            </button>
                        </div>
                        <div id="collapseOne1" class="accordion-collapse collapse"
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
                                   Uniforms and accessories are available only for the schools listed on our website. However, other back-toschool products—like bags, bottles, and more—are available for any guest to purchase.
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
                                <p>You will receive an order confirmation on your screen and an email/SMS with your order details. If you receive this confirmation, your order has been placed successfully </p>
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



<!-- Mobile Marquee Auto-Scroll Script -->
<script>
// Immediate check to prevent flash - runs synchronously
(function() {
  function quickCheck() {
    document.querySelectorAll('.category-marquee-wrapper').forEach((wrapper) => {
      const row = wrapper.querySelector('.category_box_row');
      if (row && row.children.length > 0) {
        // Temporarily reset transform to measure
        const currentTransform = row.style.transform;
        row.style.transform = 'none';
        
        const contentWidth = row.scrollWidth;
        const screenWidth = wrapper.clientWidth || wrapper.offsetWidth;
        
        // Restore transform
        row.style.transform = currentTransform;
        
        // If content fits, ensure it's centered immediately
        if (contentWidth <= screenWidth) {
          wrapper.classList.add('content-fits-screen');
          row.style.justifyContent = 'center';
        } else {
          wrapper.classList.add('content-overflows');
          row.style.justifyContent = '';
        }
        
        // Show the content after layout is determined
        wrapper.classList.add('layout-ready');
      }
    });
  }
  
  // Run immediately - use multiple strategies to catch it early
  function runCheck() {
    if (document.querySelector('.category-marquee-wrapper')) {
      quickCheck();
    } else {
      // If not found, wait a tiny bit and try again
      setTimeout(runCheck, 10);
    }
  }
  
  // Try multiple approaches to run as early as possible
  if (document.readyState === 'complete') {
    runCheck();
  } else if (document.readyState === 'interactive') {
    runCheck();
  } else {
    document.addEventListener('DOMContentLoaded', runCheck);
    // Also try immediately in case script loads after DOMContentLoaded
    runCheck();
  }
  
  // Fallback: run after a very short delay
  setTimeout(runCheck, 10);
  
  // Also try on next tick
  if (typeof requestAnimationFrame !== 'undefined') {
    requestAnimationFrame(runCheck);
  }
})();

document.addEventListener('DOMContentLoaded', function () {

  function setupTransformMarquee(wrapperSelectorOrElement, rowSelectorOrElement, options = {}) {
    const wrapper = typeof wrapperSelectorOrElement === 'string' 
      ? document.querySelector(wrapperSelectorOrElement) 
      : wrapperSelectorOrElement;
    const row = typeof rowSelectorOrElement === 'string'
      ? document.querySelector(rowSelectorOrElement)
      : rowSelectorOrElement;
    if (!wrapper || !row) return;

    const maxWidth = options.maxWidth || 768;
    
    if (!row.dataset.originalContent) {
      row.dataset.originalContent = row.innerHTML;
    }
    const originalContent = row.dataset.originalContent;
    
    function shouldAutoScroll() {
      const currentTransform = row.style.transform;
      row.style.transform = 'none';
      const contentWidth = row.scrollWidth;
      const screenWidth = wrapper.clientWidth;
      row.style.transform = currentTransform;
      return contentWidth > screenWidth;
    }

    const speedPxPerSec = typeof options.speed === 'number' ? options.speed : 20;
    let isPaused = false;
    let lastTime = null;
    let currentX = 0;
    let contentWidth = 0;
    let rafId = null;

    function measure() {
      contentWidth = row.scrollWidth / 2;
    }

    function step(timestamp) {
      if (!shouldAutoScroll() || isPaused) {
        lastTime = timestamp;
        rafId = requestAnimationFrame(step);
        return;
      }

      if (!lastTime) lastTime = timestamp;
      const delta = (timestamp - lastTime) / 1000;
      lastTime = timestamp;

      currentX -= speedPxPerSec * delta;
      if (Math.abs(currentX) >= contentWidth) {
        currentX += contentWidth;
      }

      row.style.transform = `translateX(${currentX}px)`;
      rafId = requestAnimationFrame(step);
    }

    function start() {
      cancelAnimationFrame(rafId);
      measure();
      currentX = ((currentX % contentWidth) + contentWidth) % contentWidth;
      currentX = -Math.abs(currentX);
      lastTime = null;
      rafId = requestAnimationFrame(step);
    }

    function pause() {
      isPaused = true;
    }
    
    function resume() {
      isPaused = false;
      lastTime = null;
    }

    function checkMode() {
      if (!shouldAutoScroll()) {
        wrapper.classList.add('content-fits-screen');
        wrapper.classList.remove('content-overflows');
        row.style.transform = 'none';
        row.dataset.duplicated = 'false';
        if (row.dataset.duplicated === 'true') {
          row.innerHTML = originalContent;
          row.dataset.duplicated = 'false';
        }
        pause();
        wrapper.classList.add('layout-ready');
        return;
      }
      
      wrapper.classList.remove('content-fits-screen');
      wrapper.classList.add('content-overflows');
      row.style.justifyContent = '';
      wrapper.classList.add('layout-ready');
      
      if (row.dataset.duplicated !== 'true') {
        if (originalContent && originalContent.trim() !== '') {
          row.innerHTML = originalContent + originalContent;
          row.dataset.duplicated = 'true';
          setTimeout(() => {
            measure();
            start();
          }, 100);
        }
      }
    }

    function onMouseEnter() { 
      if (shouldAutoScroll()) {
        pause();
      }
    }
    
    function onMouseLeave() { 
      if (shouldAutoScroll()) {
        resume();
      }
    }

    let resizeTimer = null;
    function onResize() {
      if (resizeTimer) clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        checkMode();
      }, 120);
    }

    wrapper.addEventListener('mouseenter', onMouseEnter);
    wrapper.addEventListener('mouseleave', onMouseLeave);
    window.addEventListener('resize', onResize);

    checkMode();

    const imgs = row.querySelectorAll('img');
    let imgsLoaded = 0;
    if (imgs.length > 0) {
      imgs.forEach(img => {
        if (img.complete) {
          imgsLoaded++;
        } else {
          img.addEventListener('load', () => {
            imgsLoaded++;
            if (imgsLoaded === imgs.length) {
              measure();
              if (row.dataset.duplicated !== 'true') {
                checkMode();
              }
            }
          }, {passive: true});
          img.addEventListener('error', () => {
            imgsLoaded++;
            if (imgsLoaded === imgs.length) {
              measure();
              if (row.dataset.duplicated !== 'true') {
                checkMode();
              }
            }
          }, {passive: true});
        }
      });
      if (imgsLoaded === imgs.length) {
        setTimeout(() => {
          measure();
          if (row.dataset.duplicated !== 'true') {
            checkMode();
          }
        }, 50);
      }
    }
  }

  function initializeMarquees() {
    document.querySelectorAll('.category-marquee-wrapper').forEach((wrapper, index) => {
      const row = wrapper.querySelector('.category_box_row');
      if (row && row.children.length > 0) {
        setupTransformMarquee(wrapper, row, { maxWidth: 99999, speed: 20, resumeDelay: 700 });
      }
    });
    setupTransformMarquee('.service-marquee-wrapper', '.service-marquee-row', { maxWidth: 1116, speed: 18, resumeDelay: 700 });
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMarquees);
  } else {
    initializeMarquees();
  }

});
</script>

@endsection
