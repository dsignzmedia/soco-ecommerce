    @extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')
<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <span class="sec-subtitle text-uppercase">Shop Now</span>
                <h2 class="sec-title">Choose how you want to continue</h2>
                <p class="sec-text">Pick the option that best describes you to continue with the ordering process.</p>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            <div class="col-md-4">
                <div class="feature-card h-100 text-center p-4 shadow-sm rounded-4" style="background-color:#ffffff;">
                    <div class="feature-icon mb-3">
                        <img src="{{ asset('assets/img/icon/ab-1-1.svg') }}" alt="Parent icon" width="64" height="64">
                    </div>
                    <h3 class="h4">Parent</h3>
                    <p>Create student profiles, manage carts and track orders for every child.</p>
                    <a href="{{ route('frontend.parent.login') }}" class="vs-btn w-100 mt-3">Sign in as Parent</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 text-center p-4 shadow-sm rounded-4" style="background-color:#ffffff;">
                    <div class="feature-icon mb-3">
                        <img src="{{ asset('assets/img/icon/sr-1-4.svg') }}" alt="School icon" width="64" height="64">
                    </div>
                    <h3 class="h4">School Partner</h3>
                    <p>Access school dashboards, manage catalogs and review bulk orders.</p>
                    <a href="{{ route('frontend.school.login') }}" class="vs-btn w-100 mt-3">Sign in as School</a>
                </div>
            </div>

        </div>
        <div class="text-center mt-5">
            <p class="mb-1 text-muted">Need help choosing?</p>
            <a href="#" class="text-primary fw-semibold">Contact our support team</a>
        </div>
    </div>
</section>
@endsection

<style>
@media (max-width: 768px) {
    /* Resize Menu Toggle Button */
    .vs-menu-toggle {
        transform: scale(0.8);
        transform-origin: center;
    }
}
</style>

