@extends('admin.layouts.base')

@section('title', 'Exchange Template | The Skool Store')
@section('page_heading', 'Exchange Template')
@section('page_subheading', 'Edit the Return-Exchange Policy page content')

@section('content')
    <div class="card" style="max-width:1200px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:15px;">
                <a href="{{ route('master.admin.settings.index') }}" style="color:#6b7280;text-decoration:none;display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid #d1d5db;background:#fff;transition:all 0.2s;" onmouseover="this.style.borderColor='#490d59';this.style.color='#490d59'" onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h3 style="margin:0;color:#111827;">Exchange Template</h3>
            </div>
        </div>

        @if(session('status'))
            <div style="padding:12px 16px;border-radius:8px;background:#ecfdf3;color:#027a48;margin-bottom:20px;border:1px solid #a7f3d0;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('master.admin.settings.exchange-template.update') }}">
            @csrf
            
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;color:#111827;font-weight:600;font-size:14px;">
                    Template Content (Blade Template)
                </label>
                <textarea 
                    name="content" 
                    id="template-content" 
                    rows="30" 
                    style="width:100%;padding:16px;border:1px solid #d1d5db;border-radius:8px;font-family:'Courier New',monospace;font-size:13px;line-height:1.6;background:#f9fafb;color:#111827;resize:vertical;"
                    required
                >{{ old('content', $content) }}</textarea>
                <p style="margin-top:8px;color:#6b7280;font-size:12px;">
                    This is the Blade template file for the Return-Exchange Policy page. Edit the content between the policy-content div.
                </p>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 20px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;background:#fff;">Cancel</a>
                <button type="submit" style="padding:10px 20px;border-radius:8px;border:none;color:#fff;background:#490d59;font-weight:600;cursor:pointer;">Save Changes</button>
            </div>
        </form>

        <div style="margin-top:30px;padding:16px;background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;">
            <h4 style="margin:0 0 12px;color:#0c4a6e;font-size:14px;font-weight:600;">Preview</h4>
            <p style="margin:0;color:#075985;font-size:13px;">
                <a href="{{ route('frontend.return-exchange') }}" target="_blank" style="color:#0284c7;text-decoration:underline;">
                    View the current Exchange Policy page →
                </a>
            </p>
        </div>
    </div>

    <style>
        #template-content:focus {
            outline:none;
            border-color:#490d59;
            background:#fff;
            box-shadow:0 0 0 3px rgba(73,13,89,0.1);
        }
    </style>
@endsection

@push('scripts')
    <!-- Open-source TinyMCE build (no API key required) -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.tinymce) return;

            tinymce.init({
                selector: '#template-content',
                base_url: 'https://cdn.jsdelivr.net/npm/tinymce@6.8.3', // load skins/plugins from same CDN
                suffix: '.min',
                height: 700,
                menubar: false,
                branding: false,
                plugins: 'code lists link table image media autoresize fullscreen',
                toolbar: 'undo redo | styles | bold italic underline | bullist numlist | link image media table | fullscreen code',
                toolbar_mode: 'sliding',
                autoresize_bottom_margin: 50,
                content_style: 'body{font-family:Inter,system-ui,sans-serif;font-size:14px;color:#111827;}',
                valid_elements: '*[*]', // keep Blade directives and custom tags intact
                convert_urls: false,
                relative_urls: false,
                remove_script_host: false,
                setup(editor) {
                    editor.on('change keyup', function() {
                        editor.save(); // sync with the textarea for form submission
                    });
                }
            });
        });
    </script>
@endpush

