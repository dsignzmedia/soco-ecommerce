@extends('inventoryadmin.layouts.base')

@section('title', 'Profile Settings | Inventory Admin')

@section('page_heading', 'Profile Settings')
@section('page_subheading', 'Update your password and view permissions')

@section('content')
<div class="card" style="margin-bottom:24px;">
    <h3 style="margin:0 0 12px;color:#0f172a;">Change Password</h3>
    <p style="margin:0 0 16px;color:#475467;">For security, please confirm your current password.</p>

    @if(session('status'))
        <div style="background:#ecfdf3;color:#027a48;border-radius:12px;padding:12px 16px;margin-bottom:12px;border:1px solid rgba(2, 122, 72, 0.25);">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef3f2;color:#b42318;border-radius:12px;padding:12px 16px;margin-bottom:12px;border:1px solid rgba(180, 35, 24, 0.25);">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.admin.profile.password.update') }}" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        @csrf
        <div style="grid-column:span 2;">
            <label for="current_password" style="display:block;margin-bottom:8px;color:#334155;font-weight:600;">Current Password</label>
            <input type="password" id="current_password" name="current_password" required style="width:100%;padding:12px;border:1px solid rgba(15, 23, 42, 0.12);border-radius:12px;">
        </div>
        <div>
            <label for="password" style="display:block;margin-bottom:8px;color:#334155;font-weight:600;">New Password</label>
            <input type="password" id="password" name="password" required style="width:100%;padding:12px;border:1px solid rgba(15, 23, 42, 0.12);border-radius:12px;">
        </div>
        <div>
            <label for="password_confirmation" style="display:block;margin-bottom:8px;color:#334155;font-weight:600;">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required style="width:100%;padding:12px;border:1px solid rgba(15, 23, 42, 0.12);border-radius:12px;">
        </div>
        <div style="grid-column:span 2;text-align:right;">
            <button type="submit" style="background:#4f46e5;color:#fff;border:none;border-radius:12px;padding:12px 20px;font-weight:600;">Update Password</button>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin:0 0 12px;color:#0f172a;">Your Permissions</h3>
    <p style="margin:0 0 16px;color:#475467;">Inventory Admin access is focused on operations.</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        @foreach($permissions as $perm)
            <div style="display:flex;align-items:center;gap:10px;border:1px solid rgba(15, 23, 42, 0.08);border-radius:12px;padding:12px;background:#fff;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:999px;background:{{ $perm['granted'] ? '#ecfdf3' : '#fef3f2' }};color:{{ $perm['granted'] ? '#027a48' : '#b42318' }};">
                    {{ $perm['granted'] ? '✓' : '!' }}
                </span>
                <div>
                    <div style="font-weight:600;color:#0f172a;">{{ $perm['label'] }}</div>
                    <div style="font-size:12px;color:#475467;">{{ $perm['granted'] ? 'Granted' : 'Not allowed' }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection