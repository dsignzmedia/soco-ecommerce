@extends('admin.layouts.base')

@section('title', 'Audit Logs | The Skool Store')
@section('page_heading', 'Audit Logs')
@section('page_subheading', 'Track who changed what and when')

@section('content')
    <div class="card" style="max-width:1200px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="margin:0;color:#111827;">Change History</h3>
            <a href="{{ route('master.admin.settings.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;font-weight:600;">← Back</a>
        </div>

        <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
            <label>
                <span>Action</span>
                <select name="action">
                    <option value="">All actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(($filters['action'] ?? null) === $action)>{{ \Illuminate\Support\Str::of($action)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Search</span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="User, entity, description">
            </label>
            <div style="display:flex;gap:12px;align-items:flex-end;">
                <button type="submit" style="padding:10px 18px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">Filter</button>
                <a href="{{ route('master.admin.settings.audit-logs') }}" style="padding:10px 18px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;text-decoration:none;">Reset</a>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;color:#6b7280;font-size:13px;text-transform:uppercase;letter-spacing:0.05em;">
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">When</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Action</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">User</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Details</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Metadata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="padding:14px;border-bottom:1px solid #f1f1f1;">
                                <strong>{{ optional($log->acted_at)->format('d M Y, H:i') ?? $log->created_at->format('d M Y, H:i') }}</strong>
                                <div style="color:#6b7280;font-size:12px;">IP: {{ $log->ip_address ?? '—' }}</div>
                            </td>
                            <td style="padding:14px;border-bottom:1px solid #f1f1f1;">
                                <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#f1f5f9;color:#0f172a;font-size:12px;font-weight:600;">
                                    {{ \Illuminate\Support\Str::of($log->action)->replace('_', ' ')->title() }}
                                </span>
                                <div style="color:#6b7280;font-size:12px;margin-top:6px;">
                                    {{ class_basename($log->entity_type ?? 'N/A') }} @if($log->entity_id)#{{ $log->entity_id }} @endif
                                </div>
                            </td>
                            <td style="padding:14px;border-bottom:1px solid #f1f1f1;">
                                <div style="font-weight:600;color:#111827;">{{ $log->user_name ?? 'System' }}</div>
                                <div style="color:#6b7280;font-size:12px;">ID: {{ $log->user_id ?? 'N/A' }}</div>
                            </td>
                            <td style="padding:14px;border-bottom:1px solid #f1f1f1;">
                                <div style="margin-bottom:6px;color:#111827;font-weight:500;">{{ $log->description ?? '—' }}</div>
                                @if(isset($log->properties['changes']))
                                    <div style="color:#6b7280;font-size:13px;">
                                        @foreach($log->properties['changes'] as $field => $change)
                                            <div><strong>{{ ucwords(str_replace('_', ' ', $field)) }}:</strong> {{ $change['before'] ?? '—' }} → {{ $change['after'] ?? '—' }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td style="padding:14px;border-bottom:1px solid #f1f1f1;font-size:12px;color:#475467;">
                                @if($log->properties)
                                    <pre style="margin:0;background:#f9fafb;padding:10px;border-radius:8px;white-space:pre-wrap;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#6b7280;">No audit entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:24px;">
            {{ $logs->links() }}
        </div>
    </div>
@endsection

