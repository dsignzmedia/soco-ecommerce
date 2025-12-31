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
                        <li><a href="{{ route('frontend.index') }}">Home</a></li>
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
                    <p class="info-text">10:00am – 06:00pm</p>
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
                <img src="{{ asset('assets/img/contact/girl_cover.jpeg') }}" alt="girl" style="max-width: 100%; height: auto; object-fit: contain;">
            </div>
            <div class="col-xl col-xxl-6 align-self-center">
                <div class="title-area">
                    <span class="sec-subtitle">Have Any questions? so plese</span>
                    <h2 class="sec-title">Feel Free to Contact!</h2>
                </div>
                <form action="{{ route('frontend.contact.submit') }}" method="POST" class="form-style3 layout2 ajax-contact">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-6 form-group">
                            <label for="firstname">First Name <span style="color: red;">*</span></label>
                            <input name="firstname" id="firstname" type="text" required minlength="2" maxlength="50" placeholder="Enter your first name">
                            <small class="error-message" id="firstname-error"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="lastname">Last Name</label>
                            <input name="lastname" id="lastname" type="text" minlength="2" maxlength="50" placeholder="Enter your last name (optional)">
                            <small class="error-message" id="lastname-error"></small>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label for="email">Email Address <span style="color: red;">*</span> </label>
                            <input name="email" id="email" type="email" maxlength="100" placeholder="Enter your email address">
                            <small class="error-message" id="email-error"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="number">Phone Number</label>
                            <input name="number" id="number" type="tel" pattern="[0-9]{10,15}" placeholder="Enter your phone number" maxlength="15">
                            <small class="error-message" id="number-error"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label for="message">Message <span style="color: red;">*</span></label>
                            <textarea name="message" id="message" cols="30" rows="10" placeholder="Type your message" required minlength="10" maxlength="1000"></textarea>
                            <small class="error-message" id="message-error"></small>
                        </div>
                        <div class="col-auto form-group">
                            <button class="vs-btn" type="submit">Send Message</button>
                        </div>
                        <p class="form-messages" style="display: none;"></p>
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

<style>
    /* Contact Form Validation Styles */
    .form-group {
        position: relative;
        margin-bottom: 20px;
    }
    
    .form-group input.is-invalid,
    .form-group textarea.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .form-group input.is-invalid:focus,
    .form-group textarea.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        outline: none;
    }
    
    .form-group input:valid:not(:placeholder-shown),
    .form-group textarea:valid:not(:placeholder-shown) {
        border-color: #28a745;
    }
    
    .error-message {
        display: none;
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: block;
        min-height: 18px;
    }
    
    .error-message:empty {
        display: none;
    }
    
    .form-group input.is-invalid ~ .error-message,
    .form-group textarea.is-invalid ~ .error-message {
        display: block;
    }
    
    .form-messages {
        margin-top: 15px;
        padding: 12px 15px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .form-messages.error {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
    
    .form-messages.success {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }
    
    .form-group label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #490d59;
        box-shadow: 0 0 0 0.2rem rgba(73, 13, 89, 0.1);
        outline: none;
    }
</style>
@endsection

