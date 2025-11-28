@extends('admin.layouts.base')

@section('title', 'Profile | The Skool Store')
@section('page_heading', 'Admin Profile')
@section('page_subheading', 'Manage your account settings and preferences')

@push('styles')
    <style>
        .profile-form {
            max-width: 600px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 48px;
            font-weight: 600;
            margin: 0 auto 24px;
        }
    </style>
@endpush

@section('content')
    <div class="card profile-form">
        <div class="profile-avatar">A</div>

        @if(session('status'))
            <div style="margin-bottom:16px;padding:12px;border-radius:8px;background:#ecfdf3;color:#065f46;">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div style="margin-bottom:16px;padding:12px;border-radius:8px;background:#fef3f2;color:#b42318;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="margin-bottom:24px;">
            <h3 style="margin:0 0 12px;color:#111827;">Change Password</h3>
            <form method="POST" action="{{ route('master.admin.profile.password.update') }}">
                @csrf
                <label>
                    <span>Current Password</span>
                    <input type="password" name="current_password" required>
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <label>
                        <span>New Password</span>
                        <input type="password" name="password" required>
                    </label>
                    <label>
                        <span>Confirm New Password</span>
                        <input type="password" name="password_confirmation" required>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" style="padding:10px 16px;border-radius:12px;border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;font-weight:600;cursor:pointer;">Update Password</button>
                </div>
                <p style="margin-top:12px;color:#6b7280;font-size:13px;">Note: You must be logged in with a valid admin session to change your password.</p>
            </form>
        </div>

        <div style="margin-top:24px;">
            <h3 style="margin:0 0 12px;color:#111827;">Your Permissions</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                @foreach(($permissions ?? []) as $perm)
                    <div style="padding:12px;border:1px solid #e5e7eb;border-radius:12px;display:flex;align-items:center;gap:8px;">
                        @if($perm['granted'])
                            <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        @else
                            <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                        @endif
                        <span style="color:#111827;font-size:14px;">{{ $perm['label'] }}</span>
                    </div>
                @endforeach
            </div>
            @if(empty($permissions))
                <p style="color:#6b7280;font-size:14px;">No permissions detected for this account.</p>
            @endif
        </div>
    </div>
@endsection

