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
                    {!! $policy->content !!}
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
