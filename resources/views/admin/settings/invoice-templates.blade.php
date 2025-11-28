@extends('admin.layouts.base')

@section('title', 'Invoice Templates | The Skool Store')
@section('page_heading', 'Invoice Templates')
@section('page_subheading', 'Design and manage invoice templates')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">Invoice Templates</h3>
            <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;">← Back</a>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            @forelse($templates as $template)
                <div style="border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:start;">
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                <h4 style="margin:0;color:#111827;">{{ $template->name }}</h4>
                                @if($template->is_default)
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#ecfdf3;color:#027a48;">Default</span>
                                @endif
                            </div>
                            <p style="margin:0;color:#6b7280;font-size:14px;">Created: {{ $template->created_at->format('M d, Y') }}</p>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="editTemplate({{ $template->id }})" style="padding:8px 12px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;font-size:14px;">Edit</button>
                            <form method="POST" action="{{ route('master.admin.settings.invoice-templates.destroy', $template) }}" style="margin:0;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #fef3f2;color:#b42318;background:#fef3f2;cursor:pointer;font-size:14px;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <p>No invoice templates created yet.</p>
                </div>
            @endforelse
        </div>

        <div style="margin-top:24px;padding:20px;border:1px dashed #d0d5dd;border-radius:16px;background:#f9fafb;">
            <h4 style="margin:0 0 16px;color:#111827;">Create New Template</h4>
            <form method="POST" action="{{ route('master.admin.settings.invoice-templates.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Template Name *</span>
                        <input type="text" name="name" required>
                    </label>
                    <label>
                        <span>Logo</span>
                        <input type="file" name="logo" accept="image/*">
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Header HTML</span>
                        <textarea name="header_html" rows="4" placeholder="<div>Header content</div>"></textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Template Content</span>
                        <textarea name="template_content" rows="8" placeholder="Invoice body HTML"></textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Footer HTML</span>
                        <textarea name="footer_html" rows="4" placeholder="<div>Footer content</div>"></textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_default" value="1">
                        <span>Set as default template</span>
                    </label>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Create Template</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($templates as $template)
        <div id="editForm{{ $template->id }}" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;max-width:800px;width:90%;max-height:90vh;overflow-y:auto;z-index:1000;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <h4 style="margin:0 0 20px;color:#111827;">Edit Invoice Template</h4>
            <form method="POST" action="{{ route('master.admin.settings.invoice-templates.update', $template) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Template Name *</span>
                        <input type="text" name="name" value="{{ $template->name }}" required>
                    </label>
                    <label>
                        <span>Logo</span>
                        <input type="file" name="logo" accept="image/*">
                        @if($template->logo_path)
                            <small style="color:#6b7280;">Current: {{ basename($template->logo_path) }}</small>
                        @endif
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Header HTML</span>
                        <textarea name="header_html" rows="4">{{ $template->header_html }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Template Content</span>
                        <textarea name="template_content" rows="8">{{ $template->template_content }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Footer HTML</span>
                        <textarea name="footer_html" rows="4">{{ $template->footer_html }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_default" value="1" @checked($template->is_default)>
                        <span>Set as default template</span>
                    </label>
                </div>
                <div style="margin-top:20px;display:flex;gap:12px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Update</button>
                    <button type="button" onclick="closeEditForm({{ $template->id }})" style="padding:10px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>
    @endforeach

    <script>
        function editTemplate(id) {
            document.getElementById('editForm' + id).style.display = 'block';
        }
        function closeEditForm(id) {
            document.getElementById('editForm' + id).style.display = 'none';
        }
    </script>
@endsection

