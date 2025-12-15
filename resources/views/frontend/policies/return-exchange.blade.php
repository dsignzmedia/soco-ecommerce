@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Return / Exchange Policy</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li>Return / Exchange Policy</li>
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
                    <p>At The SkoolStore, we want every parent and student to have a smooth and satisfying shopping experience. If something isn’t right with your order, we’re here to help.</p>

                    <h3>1. Eligibility for Return & Exchange</h3>
                    <p>You can request a return or exchange if:</p>
                    <ul>
                        <li>The product received is damaged or defective</li>
                        <li>The size does not fit</li>
                        <li>You received the wrong item</li>
                    </ul>
                    <p>(All requests will be reviewed, and only after approval, the return or exchange process will move forward.)</p>

                    <h3>2. Conditions</h3>
                    <p>To qualify:</p>
                    <ul>
                        <li>Items must be unused, unwashed, and in original condition</li>
                        <li>Tags and packaging must be intact</li>
                        <li>Request must be raised within 2 days of delivery</li>
                    </ul>

                    <h3>3. Exchange Process</h3>
                    <ul>
                        <li>Choose the correct size or product you want to exchange for</li>
                        <li>Our team will arrange pickup if applicable (based on location)</li>
                        <li>Replacement will be processed once the original product is received and inspected</li>
                    </ul>
                    <p>(All requests will be reviewed, and only after approval, the return or exchange process will move forward.)</p>

                    <h3>4. Refunds</h3>
                    <ul>
                        <li>Refunds (if applicable) will be issued as store credits or to the original payment method, based on your preference</li>
                        <li>Refunds will be processed within 7 working days after approval</li>
                    </ul>

                    <h3>5. Pickup & Delivery</h3>
                    <ul>
                        <li>Pickup availability depends on your area</li>
                        <li>If pickup is not available, customers may need to ship the product to our address—details will be provided by our team</li>
                    </ul>

                    <h3>6. Contact Us</h3>
                    <p>For any return or exchange request, please contact our support team at: <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>
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
