@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Privacy Policy</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Privacy Policy</li>
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
                    <p>Welcome to TheSkoolStore. Your privacy is important to us, and we are committed to safeguarding the personal information you share with us. This Privacy Policy explains how we collect, use, store, and protect your data when you interact with our website or services.</p>

                    <h3>2. Information We Collect</h3>
                    <h4>Personal Information</h4>
                    <p>We may collect the following personal details:</p>
                    <ul>
                        <li>Name, email address, phone number, and delivery address</li>
                        <li>Payment details (processed securely through trusted third-party payment providers)</li>
                    </ul>
                    <h4>Non-Personal Information</h4>
                    <p>We may also collect:</p>
                    <ul>
                        <li>IP address, browser type, and device information</li>
                        <li>Cookies and usage data to help improve website functionality and user experience</li>
                    </ul>

                    <h3>3. How We Use Your Information</h3>
                    <p>Your information is used solely for purposes such as:</p>
                    <ul>
                        <li>Processing and fulfilling your orders</li>
                        <li>Enhancing and improving website performance and customer experience</li>
                        <li>Sharing updates, offers, and promotional communications (only with your consent)</li>
                        <li>Meeting legal or regulatory requirements</li>
                    </ul>

                    <h3>4. Payment Security & Data Protection</h3>
                    <ul>
                        <li>We do not store or process your payment information directly. All transactions are handled securely by third-party payment processors compliant with industry security standards.</li>
                        <li>We use encryption, secure servers, and other protective measures to safeguard your data. However, please note that no online system can guarantee absolute security.</li>
                    </ul>

                    <h3>5. Sharing Your Information</h3>
                    <p>We may share your information only with trusted partners when necessary:</p>
                    <ul>
                        <li>Payment processors to securely complete your transactions</li>
                        <li>Delivery partners to ensure timely order fulfillment</li>
                        <li>Legal authorities, but only if required by law</li>
                    </ul>
                    <p>We do not sell or rent your information to third parties.</p>

                    <h3>6. Your Rights</h3>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access and review your personal information</li>
                        <li>Request corrections or deletion of your data</li>
                        <li>Opt out of marketing or promotional communications at any time</li>
                    </ul>
                    <p>For any such requests, please contact: <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>

                    <h3>7. Cookies & Tracking Technologies</h3>
                    <p>Our website uses cookies to improve functionality and enhance your browsing experience. You may adjust your browser settings to manage or disable cookies if you prefer.</p>

                    <h3>8. Updates to This Privacy Policy</h3>
                    <p>We may update this policy periodically. Any changes will be posted on this page with an updated effective date. We encourage you to review this policy regularly.</p>
                    <p>For further questions or assistance regarding this Privacy Policy, please contact us at: <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>
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
    .policy-content h4 {
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 20px;
        font-weight: 500;
        color: #333;
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
        .policy-content h4 {
            font-size: 18px;
            margin-top: 15px;
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
