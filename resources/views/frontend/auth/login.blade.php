@extends('frontend.layouts.app')
@section('no_footer', true)

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
            <p class="text-muted mb-4">Login to continue</p>
        </div>

        <!-- Sign in with Google -->
        <a href="{{ route('login.google') }}" class="btn-google w-100 mb-3">
            <svg width="20" height="20" viewBox="0 0 24 24" style="margin-right: 10px;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Sign in with Google
        </a>

        <!-- Divider -->
        <div class="divider mb-3">
            <span class="divider-text">or continue with OTP</span>
        </div>

        <!-- OTP Form -->
        <form id="loginForm">
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
                    placeholder="Enter 4-digit OTP" maxlength="4" required style="font-size: 24px; letter-spacing: 8px; font-weight: 600;"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn-send-otp">Verify OTP</button>
            </div>
            <div class="text-center">
                <button type="button" class="btn-link" id="resendOtp">
                    <i class="fas fa-redo-alt"></i> Resend OTP
                </button>
            </div>
        </form>

        <!-- Sign Up Link -->
        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account? <a href="#" class="text-primary fw-bold" id="signUpLink">Sign up for free</a></p>
        </div>
    </div>
</section>

<!-- Toast Container -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

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
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-link:hover:not(:disabled) {
        color: #3a0a47;
        background-color: #f3e5f5;
    }

    .btn-link:disabled {
        color: #999;
        cursor: not-allowed;
        text-decoration: none;
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

    /* Toast Styles */
    .custom-toast {
        background: #fff;
        border-left: 4px solid #490D59;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        min-width: 300px;
        animation: slideIn 0.3s ease-out forwards;
        opacity: 0;
        transform: translateX(100%);
    }

    .custom-toast.success { border-left-color: #28a745; }
    .custom-toast.error { border-left-color: #dc3545; }

    .custom-toast i {
        margin-right: 10px;
        font-size: 18px;
    }

    .custom-toast.success i { color: #28a745; }
    .custom-toast.error i { color: #dc3545; }

    .custom-toast .toast-message {
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 40px 25px;
        }
        #toast-container {
            left: 20px;
            right: 20px;
        }
        .custom-toast {
            min-width: auto;
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const otpForm = document.getElementById('otpForm');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const resendOtp = document.getElementById('resendOtp');
    const signUpLink = document.getElementById('signUpLink');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Custom Toast Function
    function showToast(message, type = 'error') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;

        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Replace showAlert with showToast
    function showAlert(message, type = 'error') {
        showToast(message, type);
    }

    // Google Sign In
    // document.getElementById('googleSignIn').addEventListener('click', function() {
    //     // In production, implement Google OAuth
    //     showToast('Google Sign In - To be implemented', 'info');
    // });

    // Send OTP
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const emailPhone = document.getElementById('email_phone').value;

        if (!emailPhone) {
            showToast('Please enter email or phone number', 'error');
            return;
        }

        const btn = loginForm.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Sending...';

        fetch('{{ route("auth.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ contact: emailPhone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide login form, show OTP form
                loginForm.style.display = 'none';
                otpForm.style.display = 'block';

                // Start resend timer
                startResendTimer(resendOtp);

                // For dev/demo purposes, if OTP is returned, log it
                if (data.otp) {
                    console.log('OTP:', data.otp);
                }
                showToast(data.message || 'OTP sent successfully!', 'success');
            } else {
                showToast(data.message || 'Failed to send OTP', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });

    // Verify OTP
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const otp = document.getElementById('otp').value;
        const emailPhone = document.getElementById('email_phone').value;

        if (otp.length !== 4) {
            showToast('Please enter 4-digit OTP', 'error');
            return;
        }

        const btn = otpForm.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Verifying...';

        fetch('{{ route("auth.verify-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                contact: emailPhone,
                otp: otp
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect_url || '{{ route("frontend.parent.dashboard") }}';
                }, 1000);
            } else {
                showToast(data.message || 'Invalid OTP', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });

    // Resend OTP
    resendOtp.addEventListener('click', function() {
        const emailPhone = document.getElementById('email_phone').value;
        const btn = this;

        if (btn.disabled) return;

        const originalText = 'Resend OTP';
        btn.disabled = true;
        btn.innerText = 'Sending...';

        fetch('{{ route("auth.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ contact: emailPhone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('OTP resent successfully!', 'success');
                if (data.otp) console.log('Resent OTP:', data.otp);
                startResendTimer(btn);
            } else {
                showToast(data.message || 'Failed to resend OTP', 'error');
                btn.disabled = false;
                btn.innerText = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });

    function startResendTimer(btn) {
        let timeLeft = 30;
        btn.disabled = true;
        btn.innerText = `Resend OTP in ${timeLeft}s`;

        const timerId = setInterval(() => {
            timeLeft--;
            btn.innerText = `Resend OTP in ${timeLeft}s`;

            if (timeLeft <= 0) {
                clearInterval(timerId);
                btn.disabled = false;
                btn.innerText = 'Resend OTP';
            }
        }, 1000);
    }

    // Sign Up Link
    signUpLink.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("register") }}';
    });
});
</script>
@endsection
