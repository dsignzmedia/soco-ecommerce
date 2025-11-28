@extends('admin.layouts.base')

@section('title', 'Inventory Admin Login | The Skool Store')

@section('content')
    <div style="max-width:400px;margin:100px auto;padding:40px;background:#fff;border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,0.1);">
        <div style="text-align:center;margin-bottom:32px;">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store" style="width:140px;margin-bottom:16px;">
            <h1 style="margin:0;color:#111827;font-size:24px;">Inventory Admin</h1>
            <p style="margin:8px 0 0;color:#6b7280;">Sign in to manage inventory</p>
        </div>

        <form method="POST" action="#">
            @csrf
            <label style="margin-bottom:16px;">
                <span>Email</span>
                <input type="email" name="email" required autofocus>
            </label>
            <label style="margin-bottom:20px;">
                <span>Password</span>
                <input type="password" name="password" required>
            </label>
            <button type="submit" style="width:100%;padding:12px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;margin-bottom:16px;">
                Sign In
            </button>
        </form>

        <div style="text-align:center;margin-top:24px;padding-top:24px;border-top:1px solid #e5e7eb;">
            <a href="{{ route('master.admin.login') }}" style="color:#490d59;text-decoration:none;font-size:14px;">Master Admin Login →</a>
        </div>
    </div>
@endsection

