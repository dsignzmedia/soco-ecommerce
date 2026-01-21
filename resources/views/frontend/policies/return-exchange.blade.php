@extends('frontend.layouts.app')

@section('content')
    @include('frontend.partials.header')

    <div class="breadcumb-wrapper" data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Exchange Policy</h1>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{ route('frontend.index') }}">Home</a></li>
                        <li>Exchange Policy</li>
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
                        {!! $policy->content ?? '' !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection