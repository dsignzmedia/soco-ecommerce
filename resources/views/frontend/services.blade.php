@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper " data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Our Services</h1>
            <p class="breadcumb-text">Discover Our Comprehensive Range Of Quality Services For Schools And Students</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Our Services</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #ffffff;">
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
            data-md-slide-show="2" data-autoplay="true">
            <div class="service-style1 col-xl-3">
                <div class="service-body">
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
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
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
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
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
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
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
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
                    <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;"
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
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_1.png') }}" alt="service"></a></div>
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
                <div class="service-style1">
                    <div class="service-body">
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_5.png') }}" alt="service"></a></div>
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
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/services/service (2).svg') }}" alt="service"></a></div>
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
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_4.png') }}" alt="service"></a></div>
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
                        <div class="service-img"><a href="#"><img style="height: 350px; object-fit: cover;" src="{{ asset('assets/img/indian_faces/service_2.png') }}" alt="service"></a></div>
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

    <style>
         @media (max-width: 768px) {
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
        }
    </style>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

@endsection

