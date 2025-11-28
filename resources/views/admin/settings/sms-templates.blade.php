@extends('admin.layouts.base')

@section('title', 'SMS Templates | The Skool Store')
@section('page_heading', 'SMS Templates')
@section('page_subheading', 'Manage SMS templates for notifications')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">SMS Templates</h3>
            <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;">← Back</a>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            @forelse($templates as $template)
                <div style="border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:start;">
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                <h4 style="margin:0;color:#111827;">{{ $template->name }}</h4>
                                <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#e5e7eb;color:#475467;text-transform:uppercase;">{{ $template->type }}</span>
                                @if($template->is_active)
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#ecfdf3;color:#027a48;">Active</span>
                                @else
                                    <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#fef3f2;color:#b42318;">Inactive</span>
                                @endif
                            </div>
                            <p style="margin:0;color:#6b7280;font-size:14px;">{{ \Illuminate\Support\Str::limit($template->message, 100) }}</p>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="editTemplate({{ $template->id }})" style="padding:8px 12px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;font-size:14px;">Edit</button>
                            <form method="POST" action="{{ route('master.admin.settings.sms-templates.destroy', $template) }}" style="margin:0;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #fef3f2;color:#b42318;background:#fef3f2;cursor:pointer;font-size:14px;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <p>No SMS templates created yet.</p>
                </div>
            @endforelse
        </div>

        <div style="margin-top:24px;padding:20px;border:1px dashed #d0d5dd;border-radius:16px;background:#f9fafb;">
            <h4 style="margin:0 0 16px;color:#111827;">Create New Template</h4>
            <form method="POST" action="{{ route('master.admin.settings.sms-templates.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Template Name *</span>
                        <input type="text" name="name" required>
                    </label>
                    <label>
                        <span>Type *</span>
                        <input type="text" name="type" required placeholder="order_confirmation">
                        <small style="color:#6b7280;font-size:12px;">Unique identifier (e.g., order_confirmation, otp)</small>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Message *</span>
                        <textarea name="message" rows="6" required placeholder="Your order #@{{ order_number }} has been confirmed. Thank you!"></textarea>
                        <small style="color:#6b7280;font-size:12px;">Use variables like @{{ variable_name }} for dynamic content</small>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Active</span>
                    </label>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Create Template</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($templates as $template)
        <div id="editForm{{ $template->id }}" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;max-width:600px;width:90%;max-height:90vh;overflow-y:auto;z-index:1000;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <h4 style="margin:0 0 20px;color:#111827;">Edit SMS Template</h4>
            <form method="POST" action="{{ route('master.admin.settings.sms-templates.update', $template) }}">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <label>
                        <span>Template Name *</span>
                        <input type="text" name="name" value="{{ $template->name }}" required>
                    </label>
                    <label>
                        <span>Type *</span>
                        <input type="text" name="type" value="{{ $template->type }}" required>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Message *</span>
                        <textarea name="message" rows="6" required>{{ $template->message }}</textarea>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label style="flex-direction:row;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_active" value="1" @checked($template->is_active)>
                        <span>Active</span>
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

