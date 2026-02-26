@extends('inventoryadmin.layouts.base')

@section('title', 'Invoice ' . $order->order_number)

@section('content')
<div style="max-width: 1000px; margin: 20px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <a href="{{ route('inventory.admin.orders.show', $order) }}" class="btn-back-outline" style="text-decoration: none; color: #475467; display: inline-flex; align-items: center; gap: 8px; font-weight: 500;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.5 5L7.5 10L12.5 15" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to order
        </a>
        <a href="{{ route('inventory.admin.orders.invoice-download', $order) }}" class="btn btn-primary" style="background: #490d59; border-color: #490d59; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            Download PDF
        </a>
    </div>

    @php
        $branding = \App\Models\Admin\Master\AppBranding::current();
        
        // Base64 Logo for reliability
        $logoPath = public_path('assets/img/soco_logo/logo_r_soco.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $totalTaxable = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalShipping = 0;
        $grandTotal = 0;
        $totalQty = 0;
        
        foreach($orders as $o) {
            $breakdown = $o->getTaxBreakdown();
            $totalCgst += $breakdown['cgst_amount'];
            $totalSgst += $breakdown['sgst_amount'];
            $totalIgst += $breakdown['igst_amount'];
            $totalShipping += (float)($o->shipping_cost ?? 0);
            $grandTotal += (float)$o->total_amount;
            $totalQty += (int)$o->quantity;
            
            $itemTotal = (float)$o->total_amount;
            $itemTax = (float)($o->tax_amount ?? 0);
            $itemShipping = (float)($o->shipping_cost ?? 0);
            $totalTaxable += ($itemTotal - $itemTax - $itemShipping);
        }

        try {
            $amountWords = ucwords(\Illuminate\Support\Number::spell($grandTotal)) . " Rupees Only";
        } catch (\Exception $e) {
            $amountWords = number_format($grandTotal, 2) . " Rupees Only";
        }
        
        $isInterState = $totalIgst > 0;
    @endphp

    <style>
        .invoice-card {
            background: #fff;
            border: 1px solid #000;
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: #000;
        }
        .invoice-header {
            padding: 30px;
            border-bottom: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .invoice-body { padding: 0; }
        .grid-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #000;
        }
        .info-block {
            padding: 20px;
        }
        .info-block:first-child { border-right: 1px solid #000; }
        .info-block h4 {
            margin: 0 0 12px 0;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
        }
        .info-block p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            color: #000;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table th {
            text-align: left;
            padding: 10px;
            background: #f3f3f3;
            border: 1px solid #000;
            font-size: 11px;
            font-weight: 700;
            color: #000;
        }
        .invoice-table td {
            padding: 10px;
            border: 1px solid #000;
            font-size: 13px;
            vertical-align: top;
        }
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
            border-top: 1px solid #000;
        }
        .summary-table { width: 350px; border-collapse: collapse; border: 1px solid #000; }
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #000;
            font-size: 13px;
        }
        .summary-table tr.total td {
            font-weight: 700;
            font-size: 16px;
            background: #f3f3f3;
        }
        .badge {
            padding: 4px 12px;
            border: 1px solid #000;
            font-size: 11px;
            font-weight: 700;
            color: #000;
        }
        .footer {
            padding: 20px;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .currency { font-family: 'DejaVu Sans', sans-serif; }
    </style>

    <div class="invoice-card">
        <!-- Brand Header -->
        <div class="invoice-header">
            <div style="display: flex; gap: 20px; align-items: center;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="height: 60px; width: auto;" alt="Logo">
                @else
                    <div style="border: 2px solid #490d59; padding: 10px; border-radius: 8px; font-weight: 900; color: #490d59; font-size: 24px; letter-spacing: 2px;">SOCO</div>
                @endif
                <div>
                    <div style="font-size: 14px; font-weight: 700;">{{ $branding->seller_name }}</div>
                    <div style="font-size: 12px; color: #000; max-width: 300px; margin-top: 4px;">
                        {{ $branding->seller_address ?? 'Tamil Nadu, India' }}
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 28px; font-weight: 900; color: #000;">TAX INVOICE</div>
                <div style="margin-top: 8px;">
                    <span class="badge">{{ strtoupper($order->payment_status) }}</span>
                </div>
                <div style="margin-top: 16px; font-size: 12px;">
                    Invoice #: <span style="font-weight: 700;">{{ $order->order_number }}</span><br>
                    Date: <span style="font-weight: 700;">{{ now()->format('d M, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            <!-- Addresses -->
            <div class="grid-info">
                <div class="info-block">
                    <h4>Seller Details</h4>
                    <p>
                        <strong>{{ $branding->seller_name ?? 'SOCO PRODUCTS PRIVATE LIMITED' }}</strong><br>
                        GSTIN: {{ !empty($branding->seller_gstin) && $branding->seller_gstin != '—' ? $branding->seller_gstin : '33AAGCC4236P1ZG' }}<br>
                        PAN: {{ !empty($branding->seller_pan) && $branding->seller_pan != '—' ? $branding->seller_pan : 'AAGCC4236P' }}<br>
                        FSSAI: {{ !empty($branding->seller_fssai) && $branding->seller_fssai != '—' ? $branding->seller_fssai : '12421999000123' }}
                    </p>
                </div>
                <div class="info-block" style="text-align: right;">
                    <h4>Billed To</h4>
                    <p>
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->customer_address }}<br>
                        PIN: {{ $order->pincode ?? '—' }}<br>
                        State: Tamil Nadu
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">#</th>
                        <th>Item Description</th>
                        <th style="text-align: center; width: 50px;">Qty</th>
                        <th style="text-align: right; width: 90px;">Price</th>
                        <th style="text-align: right; width: 90px;">Taxable</th>
                        <th style="text-align: right; width: 90px;">Tax</th>
                        <th style="text-align: right; width: 100px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $idx => $o)
                        @php
                            $itemTax = (float)($o->tax_amount ?? 0);
                            $itemTotal = (float)$o->total_amount;
                            $itemShip = (float)($o->shipping_cost ?? 0);
                            $itemTaxable = ($itemTotal - $itemTax - $itemShip);
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight: 700;">{{ $o->item_name }}</div>
                                <div style="font-size: 11px; color: #444; margin-top: 2px;">HSN: {{ $o->product ? $o->product->hsn_code : '—' }}</div>
                            </td>
                            <td style="text-align: center;">{{ $o->quantity }}</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format(($itemTaxable + $itemTax) / $o->quantity, 2) }}</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($itemTaxable, 2) }}</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($itemTax, 2) }}</td>
                            <td style="text-align: right; font-weight: 700;"><span class="currency">₹</span>{{ number_format($itemTaxable + $itemTax, 2) }}</td>
                        </tr>
                    @endforeach
                    @if($totalShipping > 0)
                    <tr>
                        <td style="text-align: center;">{{ count($orders) + 1 }}</td>
                        <td>Shipping & Handling Charges</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalShipping, 2) }}</td>
                        <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalShipping, 2) }}</td>
                        <td style="text-align: right;"><span class="currency">₹</span>0.00</td>
                        <td style="text-align: right; font-weight: 700;"><span class="currency">₹</span>{{ number_format($totalShipping, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <div style="padding: 20px; border-top: 1px solid #000;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Amount in words</div>
                <div style="font-size: 14px; font-weight: 700; margin-top: 4px;">{{ $amountWords }}</div>
            </div>

            <!-- Summary -->
            <div class="summary-wrapper">
                <table class="summary-table">
                    <tr>
                        <td style="font-weight: 600;">Subtotal (Taxable)</td>
                        <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalTaxable + $totalShipping, 2) }}</td>
                    </tr>
                    @if($isInterState)
                        <tr>
                            <td style="font-weight: 600;">IGST (Total)</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalIgst, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="font-weight: 600;">CGST (Total)</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalCgst, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">SGST (Total)</td>
                            <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($totalSgst, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td>Grand Total</td>
                        <td style="text-align: right;"><span class="currency">₹</span>{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <div style="max-width: 400px; border: 1px solid #000; padding: 10px;">
                <h4 style="margin: 0 0 5px 0; font-size: 10px; font-weight: 700; text-transform: uppercase;">Declaration</h4>
                <p style="margin: 0; font-size: 11px; line-height: 1.4;">
                    We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                </p>
            </div>
            <div style="text-align: center; border: 1px solid #000; padding: 15px; min-width: 200px;">
                @if($branding->signature_path)
                    <img src="{{ asset('storage/' . $branding->signature_path) }}" style="height: 50px; width: auto; margin-bottom: 5px;" alt="Signature">
                @else
                    <div style="height: 50px;"></div>
                @endif
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase;">Authorised Signatory</div>
                <div style="font-size: 11px;">For {{ $branding->seller_name }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
