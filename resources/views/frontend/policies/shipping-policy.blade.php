@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Shipping Policy</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Shipping Policy</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="policy-content">

                    <h3>1. Order Processing</h3>
                    <p>Orders are processed within 3-5 business days (excluding weekends and holidays) after receiving payment confirmation.</p>
                    <p>Once your order is processed, you will receive a confirmation email with tracking details.</p>

                    <h3>2. Shipping Charges</h3>
                    <p>Shipping charges will be calculated at checkout based on your location and order value.</p>

                    <h3>3. Estimated Delivery Time</h3>
                    <ul>
                        <li>Within Coimbatore: 3-5 business days.</li>
                        <li>Other Cities in India: 3-7 business days.</li>
                    </ul>
                    <p>Delivery times may vary based on location, weather conditions, and courier service availability.</p>

                    <h3>4. Tracking Your Order</h3>
                    <p>Once your order is shipped, you will receive a tracking link via email/SMS to monitor your shipment’s progress.</p>

                    <h3>5. Shipping Partners</h3>
                    <p>We use reliable courier services to ensure timely and safe delivery.</p>
                    <p>In case of delays, we will keep you informed and assist with updates.</p>

                    <h3>6. Order Issues & Returns</h3>
                    <p>If you receive a damaged or incorrect item, please contact us at <a href="tel:+919600833114">+91 9600833114</a> within 2 days of delivery.</p>
                    <p>For return and exchange policies, please refer to our Return & Exchange Policy.</p>

                    <h3>7. Address & Delivery Changes</h3>
                    <p>Ensure your shipping address is correct at checkout. We are unable to change the address once the order is shipped.</p>
                    <p>If your order is undeliverable due to an incorrect address, additional charges may apply for re-shipping.</p>

                    <h3>8. Contact Us</h3>
                    <p>For any shipping-related queries, reach out to us at <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a> or call us at <a href="tel:+919600833114">+91 9600833114</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .policy-content {
        color: #333;
        line-height: 1.8;
    }
    .policy-content h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: 600;
        color: #490D59;
    }
    .policy-content ul {
        list-style-type: disc;
        margin-left: 20px;
        margin-bottom: 20px;
    }
    .policy-content p {
        margin-bottom: 15px;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .policy-content h3 {
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .policy-content ul {
            margin-left: 15px;
            padding-left: 0;
        }
        .policy-content li {
            margin-left: 15px;
        }
    }
</style>
@endsection
