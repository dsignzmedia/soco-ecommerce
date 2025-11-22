@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
  About Content Section
==============================-->
<section class="space space-top">
    <div class="container">
        <div class="row align-items-center gx-60 gy-30">
            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{ asset('assets/img/about/aboutus_logo_image.png') }}" alt="About Us">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <p>At TheSkoolStore, we make school uniform shopping easy and hassle-free. As a <strong>trusted uniform manufacturer with over 7 years of experience</strong>, we have been supplying high-quality school uniforms in Coimbatore and beyond. Now, we're bringing our expertise online, making it simpler for parents to order uniforms with just a few clicks.</p>

                    <p>Our commitment to quality, timely delivery, and customer satisfaction ensures that every uniform meets the highest standards.With a commitment to quality, timely delivery, and customer satisfaction, we ensure that every uniform meets the highest standards.</p>

                    <p>TheSkoolStore is powered by <strong>SoCo Products Private Limited</strong>, our parent company and a leading name in uniform manufacturing, recognized for its reliability and excellence. We take pride in our strong focus on quality and comfort, making us the <strong>go-to uniform provider</strong> for schools and parents.</p>

                    <p>For every student, every day - making uniform shopping simple and reliable!</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

