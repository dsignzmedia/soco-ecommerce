@extends('admin.layouts.base')

@section('title', 'App Branding | The Skool Store')
@section('page_heading', 'App Branding')
@section('page_subheading', 'Customize app appearance and branding')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">App Branding</h3>
            <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;">← Back</a>
        </div>

        <form method="POST" action="{{ route('master.admin.settings.app-branding.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">Basic Information</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>App Name *</span>
                        <input type="text" name="app_name" value="{{ old('app_name', $branding->app_name) }}" required>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">Logo & Favicon</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Logo</span>
                        <input type="file" name="logo" accept="image/*">
                        @if($branding->logo_path)
                            <small style="color:#6b7280;">Current: {{ basename($branding->logo_path) }}</small>
                        @endif
                    </label>
                    <label>
                        <span>Favicon</span>
                        <input type="file" name="favicon" accept="image/*">
                        @if($branding->favicon_path)
                            <small style="color:#6b7280;">Current: {{ basename($branding->favicon_path) }}</small>
                        @endif
                    </label>
                </div>
            </div>

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">Color Scheme</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                    <label>
                        <span>Primary Color *</span>
                        <input type="color" name="primary_color" value="{{ old('primary_color', $branding->primary_color) }}" required>
                    </label>
                    <label>
                        <span>Secondary Color *</span>
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $branding->secondary_color) }}" required>
                    </label>
                    <label>
                        <span>Accent Color *</span>
                        <input type="color" name="accent_color" value="{{ old('accent_color', $branding->accent_color) }}" required>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">Typography</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Font Family</span>
                        <input type="text" name="font_family" value="{{ old('font_family', $branding->font_family) }}" placeholder="Inter, sans-serif">
                    </label>
                </div>
            </div>

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">Custom CSS</h4>
                <label>
                    <span>Custom CSS</span>
                    <textarea name="custom_css" rows="8" placeholder="/* Add custom CSS here */">{{ old('custom_css', $branding->custom_css) }}</textarea>
                </label>
            </div>

            <div style="margin-bottom:32px;">
                <h4 style="margin:0 0 16px;color:#111827;">SEO & Meta Information</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Meta Title</span>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $branding->meta_title) }}">
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Meta Description</span>
                        <textarea name="meta_description" rows="3">{{ old('meta_description', $branding->meta_description) }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Meta Keywords</span>
                        <textarea name="meta_keywords" rows="2" placeholder="keyword1, keyword2, keyword3">{{ old('meta_keywords', $branding->meta_keywords) }}</textarea>
                    </label>
                </div>
            </div>

            <div style="margin-top:32px;display:flex;gap:12px;">
                <button type="submit" style="padding:12px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Save Changes</button>
                <a href="{{ route('master.admin.settings.index') }}" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

