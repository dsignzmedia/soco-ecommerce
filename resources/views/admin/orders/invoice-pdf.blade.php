<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px; /* Slightly smaller for PDF to fit content */
            color: #000;
        }
        h2, h3, h4 { color: #000; margin: 0; }
        p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 6px; border-bottom: 1px solid #000; border-right: 1px solid #ccc; font-weight: bold; font-size: 11px; }
        td { padding: 6px; border-bottom: 1px solid #ccc; border-right: 1px solid #ccc; vertical-align: top; }
        th:last-child, td:last-child { border-right: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-border td { border: none; }
        .grand-total { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    @php
        // Calculations
        $taxAmount = $order->tax_amount ?? 0;
        $cgst = $taxAmount / 2;
        $sgst = $taxAmount / 2;
        $shipping = $order->shipping_cost ?? 0;
        $total = $order->total_amount;
        $taxableValue = $total - $taxAmount - $shipping; 
        
        $discount = 0; 
        $grossAmount = $taxableValue + $discount;
        
        // Seller Details
        $sellerName = "SoCo Products Private Limited";
        $sellerAddress = "Survey Nos. 386/2B(part), 386/2A, 381/3A2, 381/3A1, 381/2B, 387/1A, 387/1B, 387/1C, 381/3B, 387/1D, 387/1E, 382/1C, 382/1B, 387/2, 387/1F, 382/2B, 382/1D, 383/2A, 390/2, 390/1C, 383/2B, 391/1, Selakarichal village Sulur Taluk and Appanaickenpatti Village, Paladam Taluk, Coimbatore, Tamil Nadu - 641016";
        $sellerGstin = "33AAGCC4236P1ZG";
        $sellerPan = "AAGCC4236P";
        $sellerCin = "U52100DL2016PTC291626";
    @endphp

    <div style="padding: 10px;">
        
        <h2 style="text-align:center;font-weight:bold;margin-bottom:20px;">Tax Invoice</h2>
        
        <!-- Header -->
        <table class="no-border" style="margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 10px;">
            <tr>
                <td style="width: 60%; padding: 0;">
                    <strong>Sold By: {{ $sellerName }},</strong><br>
                    <strong>Ship-from Address:</strong> {{ $sellerAddress }}<br>
                    <strong>GSTIN - {{ $sellerGstin }}</strong>
                </td>
                <td style="width: 40%; text-align: right; padding: 0;">
                    <!-- Invoice Box simulation -->
                    <div style="border: 1px dashed #ccc; padding: 5px; display: inline-block; text-align: left;">
                         <strong>Invoice Number # {{ $order->order_number }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Order & Customer Info -->
        <table class="no-border" style="margin-bottom: 20px;">
            <tr>
                <td style="width: 33%; padding: 0;">
                    <p><strong>Order ID:</strong> {{ $order->order_number }}</p>
                    <p><strong>Order Date:</strong> {{ optional($order->order_date)->format('d-m-Y') }}</p>
                    <p><strong>Invoice Date:</strong> {{ now()->format('d-m-Y') }}</p>
                    <p><strong>PAN:</strong> {{ $sellerPan }}</p>
                    <p><strong>CIN:</strong> {{ $sellerCin }}</p>
                </td>
                <td style="width: 33%; padding: 0; padding-left: 10px;">
                    <strong>Bill To</strong><br>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_address }}<br>
                    Phone: {{ $order->customer_phone }}
                </td>
                <td style="width: 33%; padding: 0; padding-left: 10px;">
                    <strong>Ship To</strong><br>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_address }}<br>
                    Phone: {{ $order->customer_phone }}
                </td>
            </tr>
        </table>

        <p style="margin-bottom:10px;">Total items: 1</p>

        <!-- Product Table -->
        <table style="border: 1px solid #000; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="width: 15%;">Product</th>
                    <th style="width: 25%;">Title</th>
                    <th class="text-center" style="width: 5%;">Qty</th>
                    <th class="text-right" style="width: 10%;">Gross<br>Amount (Rs.)</th>
                    <th class="text-right" style="width: 10%;">Discounts/<br>Coupons (Rs.)</th>
                    <th class="text-right" style="width: 10%;">Taxable<br>Value (Rs.)</th>
                    <th class="text-right" style="width: 8%;">SGST/<br>UTGST (Rs.)</th>
                    <th class="text-right" style="width: 8%;">CGST<br>(Rs.)</th>
                    <th class="text-right" style="width: 9%;">Total<br>(Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $order->category ?? 'N/A' }}<br>
                        <small>HSN/SAC: —</small>
                    </td>
                    <td>
                        <strong>{{ $order->item_name }}</strong><br>
                        <small>Size: {{ $order->size }}</small><br>
                        @if($order->sku)<small>SKU: {{ $order->sku }}</small><br>@endif
                        <br>
                        <small>SGST: 9.0 %</small><br>
                        <small>CGST: 9.0 %</small>
                    </td>
                    <td class="text-center">{{ $order->quantity }}</td>
                    <td class="text-right">{{ number_format($grossAmount, 2) }}</td>
                    <td class="text-right">-{{ number_format($discount, 2) }}</td>
                    <td class="text-right">{{ number_format($taxableValue, 2) }}</td>
                    <td class="text-right">{{ number_format($sgst, 2) }}</td>
                    <td class="text-right">{{ number_format($cgst, 2) }}</td>
                    <td class="text-right">{{ number_format($taxableValue + $taxAmount, 2) }}</td>
                </tr>
                <!-- Shipping Row -->
                @if($shipping > 0)
                <tr>
                    <td colspan="5" class="text-right">Shipping Charges</td>
                    <td class="text-right">{{ number_format($shipping, 2) }}</td>
                    <td colspan="2" class="text-center">-</td>
                    <td class="text-right">{{ number_format($shipping, 2) }}</td>
                </tr>
                @endif
                <!-- Total Row -->
                <tr style="border-top: 1px solid #000; font-weight: bold;">
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($grossAmount + $shipping, 2) }}</td>
                    <td class="text-right">-{{ number_format($discount, 2) }}</td>
                    <td class="text-right">{{ number_format($taxableValue + $shipping, 2) }}</td>
                    <td class="text-right">{{ number_format($sgst, 2) }}</td>
                    <td class="text-right">{{ number_format($cgst, 2) }}</td>
                    <td class="text-right">{{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer / Grand Total -->
        <table class="no-border" style="margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="grand-total">Grand Total &nbsp;&nbsp;&nbsp; Rs. {{ number_format($total, 2) }}</div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <p style="margin-bottom: 40px;"><strong>{{ $sellerName }}</strong></p>
                    <p>Authorized Signatory</p>
                </td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; font-size: 10px; color: #666; border-top: 1px dashed #ccc; padding-top: 5px;">
            *Keep this invoice and manufacturer box for warranty purposes.
        </div>

    </div>
</body>
</html>
