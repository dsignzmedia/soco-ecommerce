@extends('admin.layouts.base')

@section('title', 'Adjust Stock | ' . $product->product_name)
@section('page_heading', 'Adjust Stock')
@section('page_subheading', $product->product_name . ' • current stock: ' . $product->inventory_stock)

@section('content')
    <div class="card" style="max-width:720px;margin:auto;">
        <form method="POST" action="{{ route('master.admin.inventory.adjust.apply', $product) }}">
            @csrf
            <label>
                <span>Current stock</span>
                <input type="number" value="{{ $product->inventory_stock }}" disabled>
            </label>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:16px;">
                <label>
                    <span>Add/Subtract qty *</span>
                    <input type="number" name="quantity_change" placeholder="Eg: +50 or -10" required>
                </label>
                <label>
                    <span>Reason *</span>
                    <select name="reason" required>
                        <option value="purchase">Purchase</option>
                        <option value="return">Return</option>
                        <option value="damage">Damage</option>
                        <option value="correction">Correction</option>
                    </select>
                </label>
            </div>
            <label style="margin-top:16px;">
                <span>Comment</span>
                <textarea name="comment" rows="3" placeholder="Reference PO, damage notes, etc."></textarea>
            </label>
            <button type="submit" style="margin-top:20px;padding:12px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;">Adjust stock</button>
            <a href="{{ route('master.admin.inventory.list') }}" style="margin-left:12px;padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;">Cancel</a>
        </form>
    </div>

    <div class="card" style="max-width:720px;margin:24px auto 0;">
        <h4 style="margin:0 0 12px;color:#111827;">Recent adjustments</h4>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Date</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Change</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Reason</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAdjustments as $adjustment)
                    <tr>
                        <td style="padding:8px;">{{ $adjustment->created_at->format('d M Y H:i') }}</td>
                        <td style="padding:8px;">{{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }} ({{ $adjustment->stock_before }} → {{ $adjustment->stock_after }})</td>
                        <td style="padding:8px;">{{ ucfirst($adjustment->reason) }}</td>
                        <td style="padding:8px;">{{ $adjustment->comment ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:8px;">No adjustments logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

