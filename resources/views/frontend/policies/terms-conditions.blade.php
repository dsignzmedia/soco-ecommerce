@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Terms & Conditions</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Terms & Conditions</li>
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

                    <h3>1. Introduction</h3>
                    <p>Welcome to TheSkoolStore. By using our website, you agree to these Terms & Conditions.</p>

                    <h3>2. Use of Website</h3>
                    <ul>
                        <li>Users must provide accurate information when placing orders.</li>
                        <li>Any misuse of the website (fraud, illegal activities) is strictly prohibited.</li>
                        <li>Bulk orders may be subject to different terms and conditions, which will be communicated at the time of order.</li>
                    </ul>

                    <h3>3. Orders, Pricing & Payments</h3>
                    <ul>
                        <li>Prices are subject to change without prior notice.</li>
                        <li>Payments must be made via approved methods on our platform.</li>
                        <li>We do not store payment details; transactions are securely handled by third-party payment processors.</li>
                    </ul>

                    <h3>4. Shipping & Exchanges</h3>
                    <ul>
                        <li>Shipping timelines are estimates and may vary based on availability.</li>
                        <li>Please check the size chart and video tutorials before placing an order. If you receive a defective or incorrect product, please contact us within 2 days for assistance.</li>
                        <li>Exchanges are accepted within 2 days if items meet exchange criteria.</li>
                    </ul>

                    <h3>5. Limitation of Liability</h3>
                    <ul>
                        <li>TheSkoolStore is not liable for indirect damage, delays, or third-party failures.</li>
                        <li>Our liability is limited to the amount paid for the order.</li>
                    </ul>

                    <h3>6. Dispute Resolution</h3>
                    <ul>
                        <li>Any disputes will first be resolved through informal negotiation.</li>
                        <li>If unresolved, arbitration will be the next step.</li>
                    </ul>

                    <h3>7. Modifications to Terms</h3>
                    <p>We may update these terms. Continued use of the website means you accept the revised terms.</p>

                    <h3>8. Contact us</h3>
                    <p>For questions, reach out to <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>
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
