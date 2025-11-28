@extends('admin.layouts.base')

@section('title', 'Backup & Restore | The Skool Store')
@section('page_heading', 'Backup & Restore')
@section('page_subheading', 'Create and manage system backups')

@section('content')
    <div class="card" style="max-width:1100px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">Backups</h3>
            <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;">← Back</a>
        </div>

        <div style="margin-bottom:32px;padding:20px;border:1px solid #e5e7eb;border-radius:16px;background:#f9fafb;">
            <h4 style="margin:0 0 16px;color:#111827;">Create New Backup</h4>
            <form method="POST" action="{{ route('master.admin.settings.backups.create') }}">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                    <label>
                        <span>Backup Type *</span>
                        <select name="type" required>
                            <option value="database">Database Only</option>
                            <option value="files">Files Only</option>
                            <option value="full">Full Backup</option>
                        </select>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <label>
                        <span>Notes (optional)</span>
                        <textarea name="notes" rows="2" placeholder="Add any notes about this backup..."></textarea>
                    </label>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" style="padding:10px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Create Backup</button>
                </div>
            </form>
        </div>

        <div>
            <h4 style="margin:0 0 16px;color:#111827;">Backup History</h4>
            @if($backups->count() > 0)
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($backups as $backup)
                        <div style="border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
                            <div style="display:flex;justify-content:space-between;align-items:start;">
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                        <h4 style="margin:0;color:#111827;">{{ $backup->name }}</h4>
                                        <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#e5e7eb;color:#475467;text-transform:uppercase;">{{ $backup->type }}</span>
                                        @if($backup->status === 'completed')
                                            <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#ecfdf3;color:#027a48;">Completed</span>
                                        @elseif($backup->status === 'pending')
                                            <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;">Pending</span>
                                        @else
                                            <span style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:#fef3f2;color:#b42318;">Failed</span>
                                        @endif
                                    </div>
                                    <p style="margin:0 0 4px;color:#6b7280;font-size:14px;">
                                        Created: {{ $backup->created_at->format('M d, Y H:i') }}
                                        @if($backup->file_size)
                                            | Size: {{ $backup->file_size_human }}
                                        @endif
                                    </p>
                                    @if($backup->notes)
                                        <p style="margin:4px 0 0;color:#6b7280;font-size:13px;font-style:italic;">{{ $backup->notes }}</p>
                                    @endif
                                </div>
                                <div style="display:flex;gap:8px;">
                                    @if($backup->status === 'completed')
                                        <a href="{{ route('master.admin.settings.backups.download', $backup) }}" style="padding:8px 12px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;background:#fff;text-decoration:none;font-size:14px;">Download</a>
                                        <form method="POST" action="{{ route('master.admin.settings.backups.restore', $backup) }}" style="margin:0;" onsubmit="return confirm('Are you sure you want to restore this backup? This will overwrite current data.')">
                                            @csrf
                                            <button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #d0d5dd;color:#475467;background:#fff;cursor:pointer;font-size:14px;">Restore</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('master.admin.settings.backups.destroy', $backup) }}" style="margin:0;" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #fef3f2;color:#b42318;background:#fef3f2;cursor:pointer;font-size:14px;">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:24px;">
                    {{ $backups->links() }}
                </div>
            @else
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <p>No backups created yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

