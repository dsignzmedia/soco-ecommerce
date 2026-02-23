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
Product Category Boxes Area
==============================-->
@php
    $branding = \App\Models\Admin\Master\AppBranding::current();
    $btsMaintenance = $branding->maintenance_bts ?? false;
    $merchMaintenance = $branding->maintenance_merch ?? false;
@endphp
<section class="product-category-boxes" style="background-color:#ffffff;">
    <div class="container">
        <div class="category-boxes-row">
            <a href="{{ $btsMaintenance ? 'javascript:void(0)' : route('frontend.shop.index', ['product_type' => 'back_to_school']) }}" 
               class="category-box-link {{ $btsMaintenance ? 'maintenance-trigger' : '' }}"
               data-maintenance-title="Back to School Products">
                <img src="{{ asset('assets/img/others/bts_product.png') }}" alt="Back to School Products" class="category-box-image desktop-only">
                <div class="category-box-content category-box-content--bg category-box-content--bts mobile-tablet-only">
                    <h3 class="category-box-title">BACK TO SCHOOL PRODUCTS</h3>
                    <p class="category-box-tagline">School essentials, beyond the ordinary.</p>
                </div>
            </a>

            <a href="{{ $merchMaintenance ? 'javascript:void(0)' : route('frontend.shop.index', ['product_type' => 'merchandised']) }}" 
               class="category-box-link {{ $merchMaintenance ? 'maintenance-trigger' : '' }}"
               data-maintenance-title="Custom Campus Merchandise">
                <img src="{{ asset('assets/img/others/merch_product.png') }}" alt="Custom Campus Merchandise" class="category-box-image desktop-only">
                <div class="category-box-content category-box-content--bg category-box-content--merch mobile-tablet-only">
                    <h3 class="category-box-title">CUSTOM CAMPUS MERCHANDISE</h3>
                    <p class="category-box-tagline">We print exactly what you want — made to match your style.</p>
                </div>
            </a>
        </div>
    </div>
</section>

<style>
/* Product Category Boxes */
.product-category-boxes {
    background: #fff;
    padding: 40px 0;
}

/* Category box link styling */
.category-box-link {
    display: block;
    text-decoration: none;
    color: inherit;
    flex: 1;
    transition: transform 0.3s ease;
}

.category-box-link:hover {
    transform: translateY(-5px);
}

/* Category box image styling */
.category-box-image {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 20px;
    object-fit: cover;
}

/* Desktop only - show images, hide cards */
@media (min-width: 992px) {
    .product-category-boxes {
        padding-top: 50px;
        padding-bottom: 50px;
    }
    
    .desktop-only {
        display: block !important;
    }
    
    .mobile-tablet-only {
        display: none !important;
    }
}

/* Tablet and Mobile - show cards, hide images */
@media (max-width: 991px) {
    .desktop-only {
        display: none !important;
    }
    
    .mobile-tablet-only {
        display: block !important;
    }
}

.category-boxes-row {
    display: flex;
    justify-content: center;
    align-items: stretch;
    gap: 15px;
    flex-wrap: nowrap;
}

.category-box {
    flex: 1;
    min-width: 0;
    max-width: none;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: all 0.3s ease;
    width: 100%;
}

.category-box-content {
    background: #ffffff;
    border: 3px solid #e0d5f0;
    border-radius: 20px;
    padding: 24px 15px 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.4s ease;
    min-height: 180px;
    width: 100%;
    box-sizing: border-box;
    overflow: visible;
}

.category-box-content--bg {
    position: relative;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 200px;
    padding: 24px 18px;
    color: #fff;
    /* Ensure text is perfectly centered vertically */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.category-box-content--bg::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 14px;
    /* soft overlay to keep text readable */
    background: linear-gradient(135deg, rgba(89, 13, 13, 0.55) 0%, rgba(100, 55, 103, 0.25) 55%, rgba(0, 0, 0, 0.15) 100%);
    z-index: 0;
}

.category-box-content--bg > * {
    position: relative;
    z-index: 1;
}

.category-box-content--bts {
    background-image: url("{{ asset('assets/img/others/BTS_Card.png') }}");
}

.category-box-content--merch {
    background-image: url("{{ asset('assets/img/others/Merchandised_Card.png') }}");
}

.category-box-content--bg .category-box-title {
    margin-top: 15px;
    color: #ffffff !important;
    text-shadow: 0 6px 16px rgb(0, 0, 0);
}

.category-box-content--bg .category-box-tagline {
    color: rgba(255,255,255,0.92) !important;
    text-shadow: 0 6px 16px rgba(0,0,0,0.18);
}

.category-box-icon {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: 16px;
    transition: transform 0.3s ease;
}

.category-box:hover .category-box-icon {
    transform: scale(1.1);
}

.category-box:hover .category-box-content {
    border-color: #490D59;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(73, 13, 89, 0.15);
}

.category-box-title {
    font-size: 16px;
    font-weight: 700;
    color: #222;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Poppins', sans-serif;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
    width: 100%;
    padding: 0;
    box-sizing: border-box;
    overflow: visible;
}

.category-box-title-red {
    color: #dc3545 !important;
}

.category-box-title-violet {
    color: #490D59 !important;
}

.category-box-tagline {
    font-size: 15px;
    color: #444;
    margin: 0;
    font-family: 'Roboto', sans-serif;
    line-height: 1.5;
    font-weight: 400;
    word-wrap: break-word;
    overflow-wrap: break-word;
    width: 100%;
    padding: 0;
    box-sizing: border-box;
    overflow: visible;
}

/* Hide taglines on tablet and mobile */
@media (max-width: 991px) {
    .category-box-tagline {
        display: none !important;
    }
    .category-box-content--bg .category-box-title {
        margin-top: 25px;
    }
    
}

/* Desktop - show taglines with proper spacing */
@media (min-width: 992px) {
    .category-box-content {
        padding: 30px 20px;
        min-height: 180px;
    }
    
    .category-box-title {
        font-size: 20px;
        margin-bottom: 12px;
    }
    
    .category-box-tagline {
        display: block;
        margin-top: 8px;
        font-size: 14px;
    }
}

.category-box:hover .category-box-title-red {
    color: #c82333 !important;
}

.category-box:hover .category-box-title-violet {
    color: #3a0a47 !important;
}

.category-box:hover .category-box-tagline {
    color: #490D59;
}

/* Hide taglines on tablet and mobile */
@media (max-width: 991px) {
    .category-box-tagline {
        display: none !important;
    }
}

/* Desktop - show taglines with proper spacing */
@media (min-width: 992px) {
    .category-box-content {
        padding: 30px 20px;
        min-height: 180px;
    }
    
    .category-box-title {
        font-size: 20px;
        margin-bottom: 12px;
    }
    
    .category-box-tagline {
        display: block;
        margin-top: 8px;
        font-size: 14px;
    }
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .product-category-boxes {
        padding: 30px 0;
    }

    .category-boxes-row {
        gap: 10px;
        flex-wrap: nowrap;
    }

    .category-box-content {
        padding: 15px 10px;
        min-height: 100px;
        border-radius: 15px;
    }

    .category-box-title {
        font-size: 12px;
        letter-spacing: 0.3px;
        line-height: 1.2;
        margin-bottom: 0;
    }
    
    .category-box-tagline {
        display: none !important;
    }
}

/* Tablet screens */
@media (min-width: 769px) and (max-width: 991px) {
    .category-boxes-row {
        gap: 15px;
    }

    .category-box-content {
        padding: 18px 12px;
        min-height: 110px;
    }

    .category-box-title {
        font-size: 14px;
        letter-spacing: 0.4px;
        margin-bottom: 0;
    }
    
    .category-box-tagline {
        display: none !important;
    }
}

/* Laptop screens */
@media (min-width: 992px) and (max-width: 1440px) {
    .category-boxes-row {
        gap: 20px;
    }
}

/* Large desktop screens */
@media (min-width: 1441px) {
    .category-box-content {
        padding: 25px 20px;
        min-height: 140px;
    }

    .category-box-title {
        font-size: 18px;
    }
}
</style>

<!--==============================
About Area
==============================--> 

<section id="aboutAreaSection" class="space-extra-bottom">
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
                <span class="sec-subtitle">10+ years, trusted quality,</span>
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
                                <p class="media-label">10+</p>
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
                    <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img
                                src="{{ asset('assets/img/indian_faces/service_1.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-1.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Comfort & Care</a></h3>
                        <p class="service-text">Soft, skin-friendly fabrics designed for all-day comfort, ensuring durability, easy care, and lasting freshness.</p>
                        <div class="service-bottom">
                            <a href="{{ route('frontend.about-us') }}" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img
                                src="{{ asset('assets/img/indian_faces/service_5.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">On Time Delivery</a></h3>
                        <p class="service-text">Reliable, punctual delivery—your school essentials arrive exactly when you need them</p>
                        <div class="service-bottom">
                            <a href="{{ route('frontend.about-us') }}" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img
                                src="{{ asset('assets/img/services/service (2).svg') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-3.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Student Delight</a></h3>
                        <p class="service-text">Designed to make every student feel confident and happy.creating a delightful experience from classroom to playground.</p>
                        <div class="service-bottom">
                            <a href="{{ route('frontend.about-us') }}" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img
                                src="{{ asset('assets/img/indian_faces/service_4.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Wide Range</a></h3>
                        <p class="service-text">A complete collection under one roof. Explore a wide range tailored to meet every school and student’s needs.</p>
                        <div class="service-bottom">
                            <a href="{{ route('frontend.about-us') }}" class="service-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img style="height: 350px; object-fit: cover;"
                                src="{{ asset('assets/img/indian_faces/service_2.png') }}" alt="service"></a></div>
                    <div class="service-content">
                        <div class="service-icon"><img src="{{ asset('assets/img/icon/check-list.svg') }}" alt="icon"></div>
                        <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Transparency</a></h3>
                        <p class="service-text">Clear, honest, and straightforward at every step. From pricing to product quality, we ensure complete visibility</p>
                        <div class="service-bottom">
                            <a href="{{ route('frontend.about-us') }}" class="service-btn">Learn More</a>
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
                        <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img src="{{ asset('assets/img/indian_faces/service_1.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-1.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Comfort & Care</a></h3>
                            <p class="service-text">Soft, skin-friendly fabrics designed for all-day comfort, ensuring durability, easy care, and lasting freshness.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img src="{{ asset('assets/img/indian_faces/service_5.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-2.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">On Time Delivery</a></h3>
                            <p class="service-text">Reliable, punctual delivery—your school essentials arrive exactly when you need them</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img src="{{ asset('assets/img/services/service (2).svg') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-3.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Student Delight</a></h3>
                            <p class="service-text">Designed to make every student feel confident and happy.creating a delightful experience from classroom to playground.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img src="{{ asset('assets/img/indian_faces/service_4.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Wide Range</a></h3>
                            <p class="service-text">A complete collection under one roof. Explore a wide range tailored to meet every school and student’s needs.</p>
                            <div class="service-bottom">
                                <a href="#" class="service-btn">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="{{ route('frontend.about-us') }}"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_2.png') }}" alt="service"></a></div>
                        <div class="service-content">
                            <div class="service-icon"><img src="{{ asset('assets/img/icon/check-list.svg') }}" alt="icon"></div>
                            <h3 class="service-title"><a href="{{ route('frontend.about-us') }}">Transparency</a></h3>
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



<!-- Service Marquee Auto-Scroll Script (kept for service section) -->
<script>
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
      const contentWidth = row.scrollWidth;
      const screenWidth = wrapper.clientWidth;
      return contentWidth > screenWidth;
    }

    const speedPxPerSec = typeof options.speed === 'number' ? options.speed : 20;
    let isPaused = false;
    let isUserScrolling = false;
    let isAutoScrolling = false;
    let lastTime = null;
    let contentWidth = 0;
    let rafId = null;
    let resumeTimeout = null;
    let lastScrollLeft = 0;
    let lastScrollTime = Date.now();

    function measure() {
      const fullWidth = row.scrollWidth;
      contentWidth = fullWidth / 2;
      if (!contentWidth || contentWidth <= 0) {
        contentWidth = wrapper.clientWidth;
      }
    }

    function step(timestamp) {
      if (!shouldAutoScroll() || isPaused || isUserScrolling) {
        lastTime = timestamp;
        rafId = requestAnimationFrame(step);
        return;
      }

      if (!lastTime) lastTime = timestamp;
      const delta = (timestamp - lastTime) / 1000;
      lastTime = timestamp;

      isAutoScrolling = true;
      let scrollLeft = wrapper.scrollLeft;
      scrollLeft += speedPxPerSec * delta;
      
      if (scrollLeft >= contentWidth) {
        scrollLeft = scrollLeft - contentWidth;
      }

      wrapper.scrollLeft = scrollLeft;
      lastScrollLeft = scrollLeft;

      setTimeout(() => {
        isAutoScrolling = false;
      }, 10);

      rafId = requestAnimationFrame(step);
    }

    function start() {
      cancelAnimationFrame(rafId);
      measure();
      lastTime = null;
      isPaused = false;
      isUserScrolling = false;
      isAutoScrolling = false;
      wrapper.scrollLeft = 0;
      lastScrollLeft = 0;
      lastScrollTime = Date.now();
      rafId = requestAnimationFrame(step);
    }

    function pause() {
      isPaused = true;
    }
    
    function resume() {
      isPaused = false;
      lastTime = null;
      if (shouldAutoScroll() && !isUserScrolling) {
        rafId = requestAnimationFrame(step);
      }
    }

    wrapper.addEventListener('scroll', function() {
      if (isAutoScrolling || isUserScrolling) {
        lastScrollLeft = wrapper.scrollLeft;
        lastScrollTime = Date.now();
        return;
      }

      const currentScrollLeft = wrapper.scrollLeft;
      const currentTime = Date.now();
      const timeDelta = currentTime - lastScrollTime;
      const scrollDelta = Math.abs(currentScrollLeft - lastScrollLeft);

      if (scrollDelta > 5 && timeDelta < 100) {
        isUserScrolling = true;
        pause();
        if (resumeTimeout) clearTimeout(resumeTimeout);
        resumeTimeout = setTimeout(function() {
        isUserScrolling = false;
          lastScrollLeft = wrapper.scrollLeft;
          lastScrollTime = Date.now();
        resume();
      }, 2000);
    }

      lastScrollLeft = currentScrollLeft;
      lastScrollTime = currentTime;
    }, { passive: true });

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
            wrapper.scrollLeft = 0;
            lastScrollLeft = 0;
            lastScrollTime = Date.now();
            start();
          }, 100);
        }
      } else {
        setTimeout(() => {
          measure();
          wrapper.scrollLeft = 0;
          lastScrollLeft = 0;
          lastScrollTime = Date.now();
          start();
        }, 50);
      }
    }

    function onMouseEnter() { 
      if (shouldAutoScroll() && !isUserScrolling) {
        pause();
      }
    }
    
    function onMouseLeave() { 
      if (shouldAutoScroll() && !isUserScrolling) {
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
              } else {
                start();
              }
            }
          }, {passive: true});
          img.addEventListener('error', () => {
            imgsLoaded++;
            if (imgsLoaded === imgs.length) {
              measure();
              if (row.dataset.duplicated !== 'true') {
                checkMode();
              } else {
                start();
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
          } else {
            start();
          }
        }, 50);
      }
    }
  }

  // Only initialize service marquee (featured products uses new simple JS)
  function initializeServiceMarquee() {
    setupTransformMarquee('.service-marquee-wrapper', '.service-marquee-row', { maxWidth: 1116, speed: 18, resumeDelay: 700 });
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeServiceMarquee);
  } else {
    initializeServiceMarquee();
  }

  // Stop carousel autoplay and navigate immediately when Learn More is clicked
  document.querySelectorAll('.service-btn[href]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      // Stop carousel autoplay if it exists
      const carousel = document.querySelector('.vs-carousel[data-autoplay="true"]');
      if (carousel) {
        // Try to access the carousel instance and stop it
        if (window.Swiper && carousel.swiper) {
          carousel.swiper.autoplay.stop();
  }
        // Also try to stop any jQuery carousel
        if (window.$ && $(carousel).data('owlCarousel')) {
          $(carousel).data('owlCarousel').stop();
        }
        // Remove autoplay attribute to prevent further transitions
        carousel.removeAttribute('data-autoplay');
      }

      // Navigate immediately - don't wait for any transitions
      const href = this.getAttribute('href');
      if (href && href !== '#') {
        window.location.href = href;
      }
    }, true); // Use capture phase to run before other handlers
  });
});
</script>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body position-relative">
                <h3 class="mt-2 h4 mb-3" id="maintenanceTitle" style="color: #490D59; font-weight: 700;">Coming Soon!</h3>
                <p class="text-muted mb-4" style="font-size: 16px;">Products will be added soon....</p>
                <button type="button" class="vs-btn style1" data-bs-dismiss="modal" style="padding: 10px 30px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const maintenanceTriggers = document.querySelectorAll('.maintenance-trigger');
    const maintenanceModal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
    const modalTitle = document.getElementById('maintenanceTitle');

    maintenanceTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const title = this.getAttribute('data-maintenance-title');
            if(title) {
                // modalTitle.textContent = title + " - Coming Soon!";
                // Keeping it simple as per request or just generic
            }
            maintenanceModal.show();
        });
    });
});
</script>
@endsection