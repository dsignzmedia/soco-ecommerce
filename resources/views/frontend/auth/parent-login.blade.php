@extends('frontend.layouts.app')

@section('content')
<section class="login-section" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f8f5ff; padding: 40px 20px;">
    <div class="login-container" style="max-width: 450px; width: 100%; background: #ffffff; border-radius: 20px; padding: 50px 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store" style="max-width: 200px; height: auto; margin-bottom: 20px;">
        </div>

        <!-- Welcome Message -->
        <div class="text-center mb-4">
            <h1 class="h2 mb-2" style="font-weight: 700; color: #333;">Welcome Back</h1>
            <p class="text-muted mb-4">Sign in to continue your shopping journey</p>
        </div>

        <!-- Sign in with Google -->
        <button type="button" class="btn-google w-100 mb-3" id="googleSignIn">
            <svg width="20" height="20" viewBox="0 0 24 24" style="margin-right: 10px;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Sign in with Google
        </button>

        <!-- Divider -->
        <div class="divider mb-3">
            <span class="divider-text">or continue with OTP</span>
        </div>

        <!-- OTP Form -->
        <form id="parentLoginForm">
            <div class="mb-3">
                <label for="email_phone" class="form-label d-flex align-items-center mb-2">
                    <i class="fas fa-phone-alt me-2" style="color: #28a745;"></i>
                    Email or Phone Number
                </label>
                <div class="d-flex gap-2">
                    <select class="form-select country-code" style="width: 120px; flex-shrink: 0;">
                        <option value="+91" selected>IN (+91)</option>
                        <option value="+1">US (+1)</option>
                        <option value="+44">UK (+44)</option>
                    </select>
                    <input type="text" class="form-control" id="email_phone" name="email_phone" 
                        placeholder="Email or phone number" required>
                </div>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn-send-otp" id="sendOtpBtn">Send OTP</button>
            </div>
        </form>

        <!-- OTP Input (shown after Send OTP) -->
        <form id="otpForm" style="display: none;">
            <div class="mb-3">
                <label for="otp" class="form-label mb-2">Enter OTP</label>
                <input type="text" class="form-control text-center" id="otp" name="otp" 
                    placeholder="Enter 6-digit OTP" maxlength="6" required style="font-size: 24px; letter-spacing: 8px; font-weight: 600;">
            </div>
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn-send-otp">Verify OTP</button>
            </div>
            <div class="text-center">
                <button type="button" class="btn-link" id="resendOtp">Resend OTP</button>
            </div>
        </form>

        <!-- Sign Up Link -->
        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account? <a href="#" class="text-primary fw-bold" id="signUpLink">Sign up for free</a></p>
        </div>
    </div>
</section>

<style>
    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #ffffff;
        color: #333;
        font-weight: 500;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-google:hover {
        background: #f8f9fa;
        border-color: #ccc;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .divider {
        position: relative;
        text-align: center;
        margin: 20px 0;
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e0e0e0;
    }

    .divider-text {
        position: relative;
        background: #ffffff;
        padding: 0 15px;
        color: #666;
        font-size: 14px;
    }

    .btn-send-otp {
        background: #490D59;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-send-otp:hover {
        background: #3a0a47;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(73, 13, 89, 0.3);
    }

    .btn-link {
        background: none;
        border: none;
        color: #490D59;
        text-decoration: underline;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-link:hover {
        color: #3a0a47;
    }

    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 40px 25px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('parentLoginForm');
    const otpForm = document.getElementById('otpForm');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const resendOtp = document.getElementById('resendOtp');
    const signUpLink = document.getElementById('signUpLink');

    // Google Sign In
    document.getElementById('googleSignIn').addEventListener('click', function() {
        // In production, implement Google OAuth
        alert('Google Sign In - To be implemented');
    });

    // Send OTP
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const emailPhone = document.getElementById('email_phone').value;
        
        if (!emailPhone) {
            alert('Please enter email or phone number');
            return;
        }

        // Hide login form, show OTP form
        loginForm.style.display = 'none';
        otpForm.style.display = 'block';
        
        // In production, send OTP via API
        alert('OTP sent to ' + emailPhone);
    });

    // Verify OTP
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const otp = document.getElementById('otp').value;
        
        if (otp.length !== 6) {
            alert('Please enter 6-digit OTP');
            return;
        }

        // In production, verify OTP via API
        // For now, redirect to dashboard
        window.location.href = '{{ route("frontend.parent.dashboard") }}';
    });

    // Resend OTP
    resendOtp.addEventListener('click', function() {
        const emailPhone = document.getElementById('email_phone').value;
        alert('OTP resent to ' + emailPhone);
    });

    // Sign Up Link
    signUpLink.addEventListener('click', function(e) {
        e.preventDefault();
        // In production, redirect to signup page
        alert('Sign up page - To be implemented');
    });
});
</script>
@endsection
