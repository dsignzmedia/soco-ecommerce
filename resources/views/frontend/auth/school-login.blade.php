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
            <h1 class="h2 mb-2" style="font-weight: 700; color: #333;">School Partner Login</h1>
            <p class="text-muted mb-4">Access your school dashboard</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Login Form -->
        <form id="schoolLoginForm" action="{{ route('frontend.school.authenticate') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label mb-2">
                    <i class="fas fa-user me-2" style="color: #490D59;"></i>
                    Username
                </label>
                <input type="text" class="form-control" id="username" name="username" 
                    placeholder="Enter your username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label mb-2">
                    <i class="fas fa-lock me-2" style="color: #490D59;"></i>
                    Password
                </label>
                <input type="password" class="form-control" id="password" name="password" 
                    placeholder="Enter your password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="#" class="text-primary small">Forgot password?</a>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn-send-otp">Sign In</button>
            </div>
        </form>

        <!-- Back to Get Started -->
        <div class="text-center mt-4">
            <a href="{{ route('frontend.get-started') }}" class="text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Back to options
            </a>
        </div>
    </div>
</section>

<style>
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

    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
    }

    .form-control:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }

    .form-check-input:checked {
        background-color: #490D59;
        border-color: #490D59;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 40px 25px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('schoolLoginForm');

    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        if (!username || !password) {
            e.preventDefault();
            alert('Please enter both username and password');
            return;
        }

        // Form will submit to backend for authentication
        // Backend will handle validation and redirect
    });
});
</script>
@endsection

