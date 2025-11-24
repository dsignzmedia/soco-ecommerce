@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')
 <div class="breadcumb-wrapper " data-bg-src="assets/img/contact/Background.png">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Contact Us</h1>
                <p class="breadcumb-text">Get In Touch With Us For Any Queries, Support, Or Assistance</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="index.html">Home</a></li>
                        <li>Contact Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<section class="space-top space-extra-bottom" style="background-color: #ffffff;">
    
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="info-style2">
                    <div class="info-icon"><img src="{{ asset('assets/img/icon/c-b-1-1.svg') }}" alt="icon"></div>
                    <h3 class="info-title">Phone No</h3>
                    <p class="info-text"><a href="tel: +91 9994878486" class="text-inherit"> +91 9994878486</a></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-style2">
                    <div class="info-icon"><img src="{{ asset('assets/img/icon/c-b-1-2.svg') }}" alt="icon"></div>
                    <h3 class="info-title">Monday to Friday</h3>
                    <p class="info-text">8.30am – 02.00pm</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-style2">
                    <div class="info-icon"><img src="{{ asset('assets/img/icon/c-b-1-3.svg') }}" alt="icon"></div>
                    <h3 class="info-title">Email Address</h3>
                    <p class="info-text"><a href="mailto: hello@theskoolstore.com" class="text-inherit"> hello@theskoolstore.com</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!--==============================
    Contact Area
    ==============================-->
<section class="space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row flex-row-reverse gx-60 justify-content-between">
            <div class="col-xl-auto">
                <img src="{{ asset('assets/img/contact/girl.png') }}" alt="girl" class="w-100">
            </div>
            <div class="col-xl col-xxl-6 align-self-center">
                <div class="title-area">
                    <span class="sec-subtitle">Have Any questions? so plese</span>
                    <h2 class="sec-title">Feel Free to Contact!</h2>
                </div>
                <form action="#" class="form-style3 layout2 ajax-contact">
                    <div class="row justify-content-between">
                        <div class="col-md-6 form-group">
                            <label>First Name <span class="required">(Required)</span></label>
                            <input name="firstname" id="firstname" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name <span class="required">(Required)</span></label>
                            <input name="lastname" id="lastname" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email Address <span class="required">(Required)</span></label>
                            <input name="email" id="email" type="email" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Phone Number <span class="required">(Required)</span></label>
                            <input name="number" id="number" type="tel" required>
                        </div>
                        <div class="col-12 form-group">
                            <label>Message <span class="required">(Required)</span></label>
                            <textarea name="message" id="message" cols="30" rows="10" placeholder="Type your message" required></textarea>
                        </div>
                        <div class="col-auto form-group">
                            <button class="vs-btn" type="submit">Send Message</button>
                        </div>
                        <p class="form-messages"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!--==============================
    Map Area
    ==============================-->
<section class="space-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="title-area">
            <h2 class="mt-n2">How To Find Us</h2>
        </div>
        <div class="map-style1">
            <iframe
                src="https://www.google.com/maps?q=No.219+Dr.Radhakrishnan+Road+Tatabad+Coimbatore+Tamil+Nadu+641012&output=embed"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
@endsection

