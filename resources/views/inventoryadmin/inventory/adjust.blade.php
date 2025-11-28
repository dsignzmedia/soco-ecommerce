@extends('inventoryadmin.layouts.base')

@section('title', 'Adjust Stock | Inventory Admin')
@section('page_heading', 'Adjust Stock')
@section('page_subheading', 'Update inventory levels manually')

@section('content')
    <div style="margin-bottom:24px;">
        <a href="{{ route('inventory.admin.inventory.index') }}" style="color:#4f46e5;font-weight:600;text-decoration:none;">← Back to Inventory</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <!-- Adjustment Form -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#0f172a;">{{ $product->product_name }}</h4>
            <div style="display:flex;gap:12px;margin-bottom:24px;">
                <div style="background:#f1f5f9;padding:12px;border-radius:8px;flex:1;">
                    <small style="color:#64748b;display:block;margin-bottom:4px;">Current Stock</small>
                    <span style="font-size:24px;font-weight:700;color:#0f172a;">{{ $product->inventory_stock }}</span>
                </div>
                <div style="background:#f1f5f9;padding:12px;border-radius:8px;flex:1;">
                    <small style="color:#64748b;display:block;margin-bottom:4px;">School</small>
                    <span style="font-weight:600;color:#0f172a;">{{ $product->school?->name }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('inventory.admin.inventory.adjust.apply', $product) }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Quantity Change (+/-)</label>
                    <input type="number" name="quantity_change" placeholder="e.g. 10 or -5" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;font-size:16px;">
                    <small style="color:#64748b;margin-top:4px;display:block;">Use negative numbers to subtract stock.</small>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Reason</label>
                    <select name="reason" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;">
                        <option value="purchase">New Purchase / Restock</option>
                        <option value="return">Customer Return</option>
                        <option value="damage">Damaged / Expired</option>
                        <option value="correction">Inventory Correction</option>
                    </select>
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Comment</label>
                    <textarea name="comment" rows="3" placeholder="Optional notes..." style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                </div>

                <button type="submit" style="width:100%;background:#4f46e5;color:#fff;border:none;padding:12px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px;">Update Stock</button>
            </form>
        </div>

        <!-- Recent Adjustments -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#0f172a;">Recent Movements</h4>
            @if($recentAdjustments->isEmpty())
                <p style="color:#94a3b8;font-style:italic;">No recent adjustments recorded.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($recentAdjustments as $adj)
                        <div style="padding-bottom:12px;border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                <span style="font-weight:600;color:#0f172a;">
                                    {{ $adj->quantity_change > 0 ? '+' : '' }}{{ $adj->quantity_change }}
                                </span>
                                <span style="font-size:12px;color:#64748b;">{{ $adj->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div style="font-size:13px;color:#475467;">
                                <span style="text-transform:capitalize;background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $adj->reason }}</span>
                                @if($adj->comment)
                                    <span style="margin-left:8px;">{{ $adj->comment }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
