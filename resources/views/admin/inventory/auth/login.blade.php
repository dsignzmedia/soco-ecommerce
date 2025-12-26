@extends('admin.layouts.base')

@section('title', 'Inventory Admin Login | The Skool Store')

@section('content')
    <div style="max-width:400px;margin:100px auto;padding:40px;background:#fff;border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,0.1);">
        <div style="text-align:center;margin-bottom:32px;">
            <img src="{{ asset('assets/img/new logo/new_logo.png') }}" alt="The Skool Store" style="width:140px;margin-bottom:16px;">
            <h1 style="margin:0;color:#111827;font-size:24px;">Inventory Admin</h1>
            <p style="margin:8px 0 0;color:#6b7280;">Sign in to manage inventory</p>
        </div>

        <form method="POST" action="#">
            @csrf
            <label style="margin-bottom:16px;display:block;">
                <span style="display:block;margin-bottom:6px;font-weight:500;color:#111827;">Email</span>
                <input type="email" name="email" required autofocus style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
            </label>
            <label style="margin-bottom:20px;display:block;position:relative;">
                <span style="display:block;margin-bottom:6px;font-weight:500;color:#111827;">Password</span>
                <input type="password" name="password" id="password" required style="width:100%;padding:12px;padding-right:45px;border:1px solid #d1d5db;border-radius:8px;">
                <button type="button" id="togglePassword" aria-label="Toggle password visibility" 
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;font-size:16px;padding:0;width:24px;height:24px;display:flex;align-items:center;justify-content:center;transition:none;">
                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                </button>
                <style>
                    #togglePassword:hover {
                        transform: translateY(-50%);
                        top: 50%;
                    }
                </style>
            </label>
            <button type="submit" style="width:100%;padding:12px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;margin-bottom:16px;">
                Sign In
            </button>
        </form>

        <script>
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const passwordIcon = document.getElementById('togglePasswordIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            });
        </script>

        <div style="text-align:center;margin-top:24px;padding-top:24px;border-top:1px solid #e5e7eb;">
            <a href="{{ route('master.admin.login') }}" style="color:#490d59;text-decoration:none;font-size:14px;">Master Admin Login →</a>
        </div>
    </div>
@endsection

