@extends('admin.layouts.merchandise')

@section('page_heading', 'Print Queue')
@section('page_subheading', 'Manage print jobs for custom merchandise.')

@section('content')
    <section class="card" style="margin-bottom:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.1);padding:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Print Jobs</h3>
            <div style="display:flex;gap:12px;">
                 <a href="{{ route('admin.merchandise.print-queue.index', ['status' => 'pending']) }}" style="padding:6px 12px;border-radius:20px;background:#fef3c7;color:#92400e;text-decoration:none;font-size:13px;font-weight:500;">Pending</a>
                 <a href="{{ route('admin.merchandise.print-queue.index', ['status' => 'printing']) }}" style="padding:6px 12px;border-radius:20px;background:#dbeafe;color:#1e40af;text-decoration:none;font-size:13px;font-weight:500;">Printing</a>
                 <a href="{{ route('admin.merchandise.print-queue.index', ['status' => 'completed']) }}" style="padding:6px 12px;border-radius:20px;background:#d1fae5;color:#065f46;text-decoration:none;font-size:13px;font-weight:500;">Completed</a>
                 <a href="{{ route('admin.merchandise.print-queue.index') }}" style="padding:6px 12px;border-radius:20px;border:1px solid #d1d5db;color:#374151;text-decoration:none;font-size:13px;font-weight:500;">All</a>
            </div>
        </div>
        
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:12px;text-align:left;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Job ID</th>
                        <th style="padding:12px;text-align:left;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Order #</th>
                        <th style="padding:12px;text-align:left;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Product</th>
                        <th style="padding:12px;text-align:left;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Status</th>
                        <th style="padding:12px;text-align:left;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Created</th>
                        <th style="padding:12px;text-align:right;font-size:12px;text-transform:uppercase;color:#6b7280;font-weight:600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($printJobs as $job)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:12px;color:#111827;font-weight:500;">#{{ $job->id }}</td>
                        <td style="padding:12px;">
                            @if($job->order)
                                <a href="{{ route('admin.merchandise.orders.show', $job->order->id) }}" style="color:#490d59;text-decoration:none;font-weight:500;">
                                    {{ $job->order->order_number }}
                                </a>
                            @else
                                <span style="color:#9ca3af;">N/A</span>
                            @endif
                        </td>
                        <td style="padding:12px;color:#374151;">{{ $job->product ? $job->product->product_name : 'Unknown Product' }}</td>
                        <td style="padding:12px;">
                            @php
                                $statusColors = [
                                    'pending' => 'background:#fff7ed;color:#c2410c;',
                                    'printing' => 'background:#eff6ff;color:#1d4ed8;',
                                    'completed' => 'background:#ecfdf5;color:#047857;',
                                    'cancelled' => 'background:#fef2f2;color:#b91c1c;',
                                ];
                                $style = $statusColors[$job->status] ?? 'background:#f3f4f6;color:#374151;';
                            @endphp
                            <span style="padding:4px 10px;border-radius:99px;font-size:12px;font-weight:600;{{ $style }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td style="padding:12px;color:#6b7280;font-size:13px;">{{ $job->created_at->format('M d, Y h:i A') }}</td>
                        <td style="padding:12px;text-align:right;">
                            <a href="{{ route('admin.merchandise.print-queue.show', $job->id) }}" style="padding:6px 12px;background:#fff;border:1px solid #d1d5db;border-radius:6px;color:#374151;text-decoration:none;font-size:13px;font-weight:500;">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:40px;text-align:center;color:#6b7280;">
                            No print jobs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:20px;">
            {{ $printJobs->links() }}
        </div>
    </section>
@endsection
