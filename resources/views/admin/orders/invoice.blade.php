@extends('admin.layouts.base')

@section('title', 'Invoice ' . $order->order_number)
@section('page_heading', 'Invoice ' . $order->order_number)

@section('content')
    <div style="max-width:960px;margin:0 auto 24px;">
        <a href="{{ route('master.admin.orders.show', $order) }}" class="btn-back-outline">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.5 5L7.5 10L12.5 15" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to order
        </a>
        <a href="{{ route('master.admin.orders.invoice.download', $order) }}" class="btn btn-primary" style="float:right;">
            Download PDF
        </a>
    </div>

    @php
        // Calculations
        $taxAmount = $order->tax_amount ?? 0;
        $cgst = $taxAmount / 2;
        $sgst = $taxAmount / 2;
        $shipping = $order->shipping_cost ?? 0;
        $total = $order->total_amount;
        // Assuming Taxable Value = Total - Tax - Shipping (if shipping is separate line item in total)
        // Adjust logic if shipping is part of taxable
        $taxableValue = $total - $taxAmount - $shipping; 
        
        // Mock Discount (Current DB might not store line-item discount explicitly, using 0 for now or reverse calc if needed)
        $discount = 0; 
        $grossAmount = $taxableValue + $discount;
        
        // Seller Details (Hardcoded as per request)
        $sellerName = "SoCo Products Private Limited";
        $sellerAddress = "Survey Nos. 386/2B(part), 386/2A, 381/3A2, 381/3A1, 381/2B, 387/1A, 387/1B, 387/1C, 381/3B, 387/1D, 387/1E, 382/1C, 382/1B, 387/2, 387/1F, 382/2B, 382/1D, 383/2A, 390/2, 390/1C, 383/2B, 391/1, Selakarichal village Sulur Taluk and Appanaickenpatti Village, Paladam Taluk, Coimbatore, Tamil Nadu - 641016";
        $sellerGstin = "33AAGCC4236P1ZG";
        $sellerPan = "AAGCC4236P";
        $sellerCin = "U52100DL2016PTC291626";
    @endphp

    <div class="card" style="max-width:960px;margin:auto;padding:30px;font-family:sans-serif;color:#000;">
        
        <h2 style="text-align:center;font-weight:bold;margin-bottom:20px;">Tax Invoice</h2>
        
        <div style="display:flex;justify-content:space-between;border-bottom:1px solid #000;padding-bottom:15px;margin-bottom:15px;">
            <div style="width:60%;font-size:12px;">
                <strong>Sold By: {{ $sellerName }},</strong><br>
                <strong>Ship-from Address:</strong> {{ $sellerAddress }}<br>
                <strong>GSTIN - {{ $sellerGstin }}</strong>
            </div>
            <div style="width:35%;text-align:right;">
                <div style="border:1px dashed #ccc;padding:5px;display:inline-block;text-align:left;">
                    <p style="margin:0;font-weight:bold;">Invoice Number # {{ $order->order_number }}</p>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:20px;">
            <div style="width:30%;">
                <p style="margin:2px 0;"><strong>Order ID:</strong> {{ $order->order_number }}</p>
                <p style="margin:2px 0;"><strong>Order Date:</strong> {{ optional($order->order_date)->format('d-m-Y') }}</p>
                <p style="margin:2px 0;"><strong>Invoice Date:</strong> {{ now()->format('d-m-Y') }}</p>
                <p style="margin:2px 0;"><strong>PAN:</strong> {{ $sellerPan }}</p>
                <p style="margin:2px 0;"><strong>CIN:</strong> {{ $sellerCin }}</p>
            </div>
            <div style="width:30%;">
                <strong>Bill To</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_address }}<br>
                Phone: {{ $order->customer_phone }}
            </div>
            <div style="width:30%;">
                <strong>Ship To</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_address }}<br>
                Phone: {{ $order->customer_phone }}
            </div>
        </div>

        <p style="margin-bottom:10px;font-size:13px;">Total items: 1</p>

        <table style="width:100%;border-collapse:collapse;font-size:12px;margin-bottom:20px;border:1px solid #000;">
            <thead>
                <tr style="border-bottom:1px solid #000;">
                    <th style="padding:8px;text-align:left;border-right:1px solid #ccc;">Product</th>
                    <th style="padding:8px;text-align:left;border-right:1px solid #ccc;">Title</th>
                    <th style="padding:8px;text-align:center;border-right:1px solid #ccc;">Qty</th>
                    <th style="padding:8px;text-align:right;border-right:1px solid #ccc;">Gross Amount ₹</th>
                    <th style="padding:8px;text-align:right;border-right:1px solid #ccc;">Discounts/Coupons ₹</th>
                    <th style="padding:8px;text-align:right;border-right:1px solid #ccc;">Taxable Value ₹</th>
                    <th style="padding:8px;text-align:right;border-right:1px solid #ccc;">SGST/UTGST ₹</th>
                    <th style="padding:8px;text-align:right;border-right:1px solid #ccc;">CGST ₹</th>
                    <th style="padding:8px;text-align:right;">Total ₹</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px;border-right:1px solid #ccc;vertical-align:top;">
                        {{ $order->category ?? 'N/A' }}<br>
                        <small>HSN/SAC: —</small>
                    </td>
                    <td style="padding:8px;border-right:1px solid #ccc;vertical-align:top;">
                        <strong>{{ $order->item_name }}</strong><br>
                        <small>Size: {{ $order->size }}</small><br>
                        @if($order->sku)<small>SKU: {{ $order->sku }}</small><br>@endif
                        <br>
                        <small>SGST: 9.0 %</small><br>
                        <small>CGST: 9.0 %</small>
                    </td>
                    <td style="padding:8px;text-align:center;border-right:1px solid #ccc;vertical-align:top;">{{ $order->quantity }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;vertical-align:top;">{{ number_format($grossAmount, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;vertical-align:top;">-{{ number_format($discount, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;vertical-align:top;">{{ number_format($taxableValue, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;vertical-align:top;">{{ number_format($sgst, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;vertical-align:top;">{{ number_format($cgst, 2) }}</td>
                    <td style="padding:8px;text-align:right;vertical-align:top;">{{ number_format($taxableValue + $taxAmount, 2) }}</td>
                </tr>
                <!-- Shipping Row if applicable -->
                @if($shipping > 0)
                <tr style="border-top:1px dashed #ccc;">
                    <td colspan="5" style="padding:8px;text-align:right;border-right:1px solid #ccc;">Shipping Charges</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">{{ number_format($shipping, 2) }}</td>
                    <td colspan="2" style="padding:8px;text-align:right;border-right:1px solid #ccc;">-</td>
                    <td style="padding:8px;text-align:right;">{{ number_format($shipping, 2) }}</td>
                </tr>
                @endif
                <tr style="border-top:1px solid #000;font-weight:bold;">
                    <td colspan="3" style="padding:8px;text-align:right;border-right:1px solid #ccc;">Total</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">{{ number_format($grossAmount + $shipping, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">-{{ number_format($discount, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">{{ number_format($taxableValue + $shipping, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">{{ number_format($sgst, 2) }}</td>
                    <td style="padding:8px;text-align:right;border-right:1px solid #ccc;">{{ number_format($cgst, 2) }}</td>
                    <td style="padding:8px;text-align:right;">{{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-top:30px;">
             <div style="width:50%;">
                 <h3 style="margin:0;">Grand Total &nbsp;&nbsp;&nbsp; ₹ {{ number_format($total, 2) }}</h3>
             </div>
             <div style="width:50%;text-align:right;">
                 <p style="margin:0 0 40px;"><strong>{{ $sellerName }}</strong></p>
                 <p style="margin:0;">Authorized Signatory</p>
             </div>
        </div>
        
    </div>
@endsection

