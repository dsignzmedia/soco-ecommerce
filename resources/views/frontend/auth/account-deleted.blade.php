@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Account Deleted Message -->
<section class="space-top space-extra-bottom" style="background-color: #f8f5ff; min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden;">
                    
                    <!-- Header with Icon -->
                    <div style="background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 40px; text-align: center;">
                        <div style="width: 100px; height: 100px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-exclamation-circle" style="font-size: 50px; color: #fff;"></i>
                        </div>
                        <h2 style="color: #fff; font-weight: 700; margin: 0; font-size: 28px;">Account Deactivated</h2>
                    </div>

                    <!-- Body -->
                    <div class="card-body" style="padding: 40px;">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <p style="font-size: 16px; color: #374151; line-height: 1.8; margin-bottom: 20px;">
                                Your school account has been <strong style="color: #dc2626;">deactivated</strong> by the system administrator.
                            </p>
                            <p style="font-size: 15px; color: #6b7280; line-height: 1.6;">
                                This usually happens when a school account is removed from the active system. All associated data including products, orders, and student information has been archived.
                            </p>
                        </div>

                        <!-- Contact Information -->
                        <div style="background: #fef2f2; border: 2px solid #fecaca; border-radius: 12px; padding: 25px; margin-bottom: 25px;">
                            <h5 style="color: #991b1b; font-weight: 600; margin-bottom: 15px; font-size: 16px;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Need Help?
                            </h5>
                            <p style="color: #7f1d1d; font-size: 14px; margin-bottom: 15px;">
                                Please contact the SOCO Team for more information or to reactivate your account:
                            </p>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <a href="mailto:hello@theskoolstore.com" style="display: flex; align-items: center; gap: 10px; color: #dc2626; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-envelope" style="width: 20px;"></i>
                                    <span>hello@theskoolstore.com</span>
                                </a>
                                <a href="tel:+919994878486" style="display: flex; align-items: center; gap: 10px; color: #dc2626; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-phone" style="width: 20px;"></i>
                                    <span>+91 9994878486</span>
                                </a>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <a href="{{ route('login') }}" class="vs-btn" style="width: 100%; text-align: center; background: linear-gradient(135deg, #490D59, #6a1b7a); border: none; padding: 14px;">
                                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>Back to Login
                            </a>
                            <a href="{{ url('/') }}" style="width: 100%; text-align: center; padding: 14px; border: 2px solid #d1d5db; background: #fff; border-radius: 8px; color: #374151; text-decoration: none; font-weight: 600; display: block;">
                                <i class="fas fa-home" style="margin-right: 8px;"></i>Go to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
