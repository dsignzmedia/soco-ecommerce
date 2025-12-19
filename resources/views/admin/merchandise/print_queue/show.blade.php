@extends('admin.layouts.merchandise')

@section('page_heading', 'Print Job Details')
@section('page_subheading', 'View and update print job status.')

@section('content')
@section('content')
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <!-- Job Details -->
        <section class="card" style="border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.1);padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h3 style="margin:0;font-size:18px;font-weight:600;color:#111827;">Print Job #{{ $printJob->id }}</h3>
                @php
                    $statusColors = [
                        'pending' => 'background:#fff7ed;color:#c2410c;',
                        'printing' => 'background:#eff6ff;color:#1d4ed8;',
                        'completed' => 'background:#ecfdf5;color:#047857;',
                        'cancelled' => 'background:#fef2f2;color:#b91c1c;',
                    ];
                    $style = $statusColors[$printJob->status] ?? 'background:#f3f4f6;color:#374151;';
                @endphp
                <span style="padding:4px 12px;border-radius:99px;font-size:13px;font-weight:600;{{ $style }}">{{ ucfirst($printJob->status) }}</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                <div>
                    <span style="display:block;font-size:12px;font-weight:600;color:#6b7280;margin-bottom:4px;text-transform:uppercase;">Order Number</span>
                    @if($printJob->order)
                        <a href="{{ route('admin.merchandise.orders.show', $printJob->order->id) }}" style="font-size:15px;color:#490d59;font-weight:600;text-decoration:none;">{{ $printJob->order->order_number }}</a>
                    @else
                        <span style="color:#9ca3af;">N/A</span>
                    @endif
                </div>
                <div>
                    <span style="display:block;font-size:12px;font-weight:600;color:#6b7280;margin-bottom:4px;text-transform:uppercase;">Product Item</span>
                    <span style="font-size:15px;color:#111827;">{{ $printJob->product ? $printJob->product->product_name : 'Unknown Product' }}</span>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <h4 style="font-size:14px;font-weight:600;color:#111827;margin:0 0 12px;">Custom Print Details</h4>
                <div style="background:#f9fafb;padding:16px;border-radius:8px;border:1px solid #e5e7eb;">
                    @if(!empty($printJob->details['customer_text']))
                        <div style="margin-bottom:12px;">
                            <span style="font-size:12px;color:#6b7280;font-weight:600;text-transform:uppercase;">Custom Text</span>
                            <div style="margin-top:4px;font-size:14px;color:#111827;font-family:monospace;background:white;padding:8px;border:1px solid #e5e7eb;border-radius:4px;">{{ $printJob->details['customer_text'] }}</div>
                        </div>
                    @endif

                    @if(!empty($printJob->details['design_file']))
                        <div style="margin-bottom:12px;">
                            <span style="font-size:12px;color:#6b7280;font-weight:600;text-transform:uppercase;">Design File</span>
                            <div style="margin-top:4px;">
                                <a href="{{ asset($printJob->details['design_file']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;background:#fff;border:1px solid #d1d5db;border-radius:6px;text-decoration:none;color:#374151;font-size:13px;font-weight:500;">
                                    <i class="fas fa-file-download"></i> Download Design
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(empty($printJob->details['customer_text']) && empty($printJob->details['design_file']))
                        <p style="margin:0;color:#6b7280;font-style:italic;font-size:13px;">No specific custom details provided (Standard Print).</p>
                    @endif
                </div>
            </div>
            
             <div style="margin-top:24px;">
                 <span style="display:block;font-size:12px;font-weight:600;color:#6b7280;margin-bottom:8px;text-transform:uppercase;">Raw Data</span>
                 <pre style="background:#111827;color:#e5e7eb;padding:12px;border-radius:8px;font-size:12px;overflow-x:auto;">{{ json_encode($printJob->details, JSON_PRETTY_PRINT) }}</pre>
             </div>
        </section>

        <!-- Actions -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <section class="card" style="border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.1);padding:24px;">
                <h4 style="margin:0 0 16px;font-size:15px;font-weight:600;color:#111827;">Update Status</h4>
                <form action="{{ route('admin.merchandise.print-queue.update', $printJob->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <label style="display:block;margin-bottom:8px;font-size:13px;color:#374151;font-weight:500;">Current Status</label>
                    <div style="position:relative;">
                        <select name="status" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:14px;background:#fff;color:#111827;appearance:none;outline:none;">
                            <option value="pending" {{ $printJob->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="printing" {{ $printJob->status == 'printing' ? 'selected' : '' }}>Printing (Started)</option>
                            <option value="completed" {{ $printJob->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $printJob->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#6b7280;pointer-events:none;font-size:12px;"></i>
                    </div>

                    <button type="submit" style="margin-top:16px;width:100%;padding:10px;background:#490d59;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;">Update Status</button>
                </form>
            </section>

            @if($printJob->print_file_path)
            <section class="card" style="border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.1);padding:24px;">
                 <h4 style="margin:0 0 16px;font-size:15px;font-weight:600;color:#111827;">Production File</h4>
                 <a href="{{ asset($printJob->print_file_path) }}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px;">
                     <i class="fas fa-download"></i> Download Production File
                 </a>
                 <p style="margin:12px 0 0;font-size:12px;color:#6b7280;text-align:center;">This is the final file generated for the printer.</p>
            </section>
            @endif
        </div>
    </div>
@endsection
