@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper " data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">About Us</h1>
            <p class="breadcumb-text">Learn More About Our Mission, Values, And Commitment To Quality Education</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>About Us</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!--==============================
  About Content Section
==============================-->
<section class="space space-top" style="background-color: #ffffff;">
    <div class="container">
        <div class="about-wrapper">
            <div class="about-content-wrapper">
                <!-- Floating image on the left -->
                <div class="about-img-float">
                    <img src="{{ asset('assets/img/about/aboutus_logo_image.png') }}" alt="About Us">
                </div>
                <div class="about-intro">
                <!-- First paragraph wraps around the image -->
                <p>At TheSkoolStore, we make school uniform shopping easy and hassle-free. As a <strong>trusted uniform manufacturer with over 7 years of experience</strong>, we have been supplying high-quality school uniforms in Coimbatore and beyond. Now, we're bringing our expertise online, making it simpler for parents to order uniforms with just a few clicks.</p>
                </div>
                
                <!-- Remaining content flows below in full width -->
                <div class="about-full-content">
                    <p>Our commitment to quality, timely delivery, and customer satisfaction ensures that every uniform meets the highest standards. With a commitment to quality, timely delivery, and customer satisfaction, we ensure that every uniform meets the highest standards.</p>

                    <p>TheSkoolStore is powered by <strong>SoCo Products Private Limited</strong>, our parent company and a leading name in uniform manufacturing, recognized for its reliability and excellence. We take pride in our strong focus on quality and comfort, making us the <strong>go-to uniform provider</strong> for schools and parents.</p>

                    <p>For every student, every day - making uniform shopping simple and reliable!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .about-intro {
        text-align: justify !important;
    }   
    .about-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .about-content-wrapper {
        font-size: 16px;
        line-height: 1.8;
        color: #333;
        text-align: justify;
    }

    .about-img-float {
        float: left;
        width: 390px;
        margin-right: 30px;
        margin-bottom: 20px;
    }

    .about-img-float img {
        width: 100%;
        height: auto;
    }

    .about-intro {
        margin-bottom: 20px;
        text-align: justify;
    }

    .about-full-content {
        clear: both;
        margin-top: 20px;
    }

    .about-full-content p {
        margin-bottom: 20px;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .about-img-float {
            float: none;
            width: 100%;
            max-width: 300px;
            margin: 0 auto 20px auto;
            display: block;
        }

        .about-intro {
            text-align: left;
        }
    }
</style>
@endsection

