<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tax Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
        .container {
            border: 1px solid #000;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: top;
            padding: 4px 6px;
            border: 1px solid #000;
        }
        .no-border-table td, .no-border-table th {
            border: none !important;
            padding: 1px 0;
        }
        .header-logo { width: 130px; }
        .tax-invoice-title { font-size: 18px; font-weight: bold; text-align: right; border: none; padding-top: 15px; padding-bottom: 20px;}
        
        .bold { font-weight: bold; }
        .section-label { font-size: 7.5px; color: #333; margin-bottom: 2px; }
        
        .product-table th {
            font-weight: bold;
            text-align: center;
            background-color: #fff;
            font-size: 7px;
            padding: 3px;
        }
        .product-table td {
            text-align: center;
            font-size: 7px;
            padding: 3px;
        }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        
        .footer-note {
            padding: 8px;
            font-size: 7.5px;
            border-top: 1px solid #000;
        }
        .terms {
            font-size: 7px;
            padding: 8px;
            border-top: 1px solid #000;
        }
        .terms ol { margin: 3px 0; padding-left: 12px; }
    </style>
</head>
<body>
    @php
        $branding = \App\Models\Admin\Master\AppBranding::current();
        
        // GD Extension Check for reliability in PDFs
        $hasGD = extension_loaded('gd');
        
        // Base64 Logo for reliability in PDFs
        $logoPath = public_path('assets/img/soco_logo/logo_r_soco.png');
        $logoBase64 = '';
        if ($hasGD && file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        // --- GROUPED CALCULATION ---
        $totalTax = 0;
        $totalShipping = 0;
        $grandTotal = 0;
        $totalQuantity = 0;
        
        foreach($orders as $o) {
            $totalTax += (float)($o->tax_amount ?? 0);
            $totalShipping += (float)($o->shipping_cost ?? 0);
            $grandTotal += (float)$o->total_amount;
            $totalQuantity += (int)$o->quantity;
        }
        
        $cgstTotal = $totalTax / 2;
        $sgstTotal = $totalTax / 2;

        try {
            $amountInWords = ucwords(\Illuminate\Support\Number::spell($grandTotal)) . " Rupees Only";
        } catch (\Exception $e) {
            $amountInWords = number_format($grandTotal, 2) . " Rupees Only";
        }
    @endphp

    <div class="container">
        <!-- Header -->
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 50%; padding: 10px;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-width: 150px; height: auto;" alt="SOCO Logo">
                    @else
                        <div class="bold" style="font-size: 14px;">SOCO PRODUCTS</div>
                    @endif
                </td>
                <td style="border: none; width: 50%; padding: 10px;" class="tax-invoice-title">
                    Tax Invoice
                </td>
            </tr>
        </table>

        <!-- Seller Block -->
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="section-label">Sold By / Seller</div>
                    <div class="bold" style="font-size: 9px; margin-bottom: 2px;">{{ $branding->seller_name ?? 'SOCO PRODUCTS PRIVATE LIMITED' }}</div>
                    <div>{{ $branding->seller_address ?? 'Survey Nos. 386/2B(part), 386/2A, Coimbatore, Tamil Nadu - 641016' }}</div>
                    <div style="margin-top: 5px;">
                        <table class="no-border-table">
                            <tr><td style="width: 100px;" class="bold">GSTIN</td><td>: {{ !empty($branding->seller_gstin) && $branding->seller_gstin != '—' ? $branding->seller_gstin : '33AAGCC4236P1ZG (Ref)' }}</td></tr>
                            <tr><td class="bold">FSSAI License Number</td><td>: {{ !empty($branding->seller_fssai) && $branding->seller_fssai != '—' ? $branding->seller_fssai : '12421999000123 (Ref)' }}</td></tr>
                            <tr><td class="bold">CIN</td><td>: {{ !empty($branding->seller_cin) && $branding->seller_cin != '—' ? $branding->seller_cin : 'U52100DL2016PTC291626 (Ref)' }}</td></tr>
                            <tr><td class="bold">PAN</td><td>: {{ !empty($branding->seller_pan) && $branding->seller_pan != '—' ? $branding->seller_pan : 'AAGCC4236P (Ref)' }}</td></tr>
                        </table>
                    </div>
                </td>
                <td style="width: 35%; text-align: center;">
                    @if($hasGD)
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($order->order_number) }}&format=jpg" style="width: 60px; height: 60px;">
                    @endif
                    <div style="margin-top: 4px; font-size: 7px;">Invoice Number : {{ $order->order_number }}</div>
                </td>
            </tr>
        </table>

        <!-- Customer & Order Info -->
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="bold" style="margin-bottom: 3px;">Invoice To</div>
                    <table class="no-border-table">
                        <tr><td style="width: 80px;" class="bold">Name</td><td>: {{ $order->customer_name }}</td></tr>
                        <tr><td class="bold" style="vertical-align: top;">Address</td><td>: {{ $order->customer_address }}</td></tr>
                        <tr><td class="bold">Pin code</td><td>: {{ $order->pincode ?? '—' }}</td></tr>
                        <tr><td class="bold">State</td><td>: Tamil Nadu</td></tr>
                    </table>
                </td>
                <td style="width: 35%;">
                    <table class="no-border-table">
                        <tr><td style="width: 80px;" class="bold">Order Id</td><td>: {{ $order->order_number }}</td></tr>
                        <tr><td class="bold">Invoice Date</td><td>: {{ now()->format('d-M-Y') }}</td></tr>
                        <tr><td class="bold">Place of Supply</td><td>: Tamil Nadu</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Product Table -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>Sr. no</th>
                    <th>UPC</th>
                    <th style="width: 130px;">Item Description</th>
                    <th>MRP</th>
                    <th>Discount</th>
                    <th>Qty.</th>
                    <th>Taxable Value</th>
                    <th>CGST (%)</th>
                    <th>CGST (INR)</th>
                    <th>SGST (%)</th>
                    <th>SGST (INR)</th>
                    <th>Cess (%)</th>
                    <th>Addl Cess</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $index => $o)
                    @php
                        $itemHSN = $o->product ? $o->product->hsn_code : '—';
                        $itemTax = (float)($o->tax_amount ?? 0);
                        $itemShipping = (float)($o->shipping_cost ?? 0);
                        $itemTotal = (float)$o->total_amount;
                        $itemTaxable = $itemTotal - $itemTax - $itemShipping;
                        
                        $itemGstRate = 0;
                        if($o->product && $o->product->tax_profile) {
                            preg_match('/\d+/', $o->product->tax_profile, $matches);
                            $itemGstRate = $matches[0] ?? 0;
                        }
                        $itemHalfRate = $itemGstRate / 2;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>—</td>
                        <td class="text-left">
                            <div class="bold">{{ $o->item_name }}</div>
                            <div style="font-size: 6.5px; color: #444;">(HSN-{{ $itemHSN }})</div>
                        </td>
                        <td>{{ number_format($itemTaxable + $itemTax, 2) }}</td>
                        <td>0.00</td>
                        <td>{{ $o->quantity }}</td>
                        <td>{{ number_format($itemTaxable, 2) }}</td>
                        <td>{{ number_format($itemHalfRate, 2) }}</td>
                        <td>{{ number_format($itemTax / 2, 2) }}</td>
                        <td>{{ number_format($itemHalfRate, 2) }}</td>
                        <td>{{ number_format($itemTax / 2, 2) }}</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="bold">{{ number_format($itemTaxable + $itemTax, 2) }}</td>
                    </tr>
                @endforeach

                @if($totalShipping > 0)
                <tr>
                    <td>{{ count($orders) + 1 }}</td>
                    <td>—</td>
                    <td class="text-left">Shipping Charges</td>
                    <td>{{ number_format($totalShipping, 2) }}</td>
                    <td>0.00</td>
                    <td>1</td>
                    <td>{{ number_format($totalShipping, 2) }}</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td class="bold">{{ number_format($totalShipping, 2) }}</td>
                </tr>
                @endif
                <tr class="bold" style="background-color: #fafafa;">
                    <td colspan="5" class="text-left">Total</td>
                    <td>{{ $totalQuantity + ($totalShipping > 0 ? 1 : 0) }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($cgstTotal, 2) }}</td>
                    <td></td>
                    <td>{{ number_format($sgstTotal, 2) }}</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Amount Words -->
        <div class="footer-note bold">
            Amount in Words: &nbsp;&nbsp;&nbsp; {{ $amountInWords }}
        </div>

        <!-- Authorized Signatory Repeat -->
        <table style="border-top: 1px solid #000;">
            <tr>
                <td style="width: 70%;">
                    <div class="bold" style="margin-bottom: 3px;">{{ $branding->seller_name ?? 'SOCO PRODUCTS PRIVATE LIMITED' }}</div>
                    <table class="no-border-table">
                        <tr>
                            <td class="bold" style="width: 40px;">GSTIN</td><td style="width: 140px;">: {{ !empty($branding->seller_gstin) && $branding->seller_gstin != '—' ? $branding->seller_gstin : '33AAGCC4236P1ZG (Ref)' }}</td>
                            <td class="bold" style="width: 110px;">FSSAI License Number</td><td>: {{ !empty($branding->seller_fssai) && $branding->seller_fssai != '—' ? $branding->seller_fssai : '12421999000123 (Ref)' }}</td>
                        </tr>
                        <tr>
                            <td class="bold">CIN</td><td>: {{ !empty($branding->seller_cin) && $branding->seller_cin != '—' ? $branding->seller_cin : 'U52100DL2016PTC291626 (Ref)' }}</td>
                            <td class="bold">PAN</td><td>: {{ !empty($branding->seller_pan) && $branding->seller_pan != '—' ? $branding->seller_pan : 'AAGCC4236P (Ref)' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%; text-align: center; vertical-align: bottom; border-left: 1px solid #000;">
                    <div style="margin-bottom: 8px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Jon_Kirsch_Signature.png" style="width: 60px; height: auto;">
                    </div>
                    <div class="bold">Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <!-- Reverse Charge -->
        <div class="bold" style="padding: 4px 6px; border-top: 1px solid #000;">
            Whether the tax is payable on reverse charge - No
        </div>

        <!-- Terms -->
        <div class="terms">
            <div class="bold">Terms & Conditions:</div>
            <ol>
                <li>If you have any issues or queries in respect of your order, please contact customer chat support through platform or drop in email at {{ $branding->meta_description ?? 'support@theskoolstore.com' }}</li>
                <li>In case you need to get more information about seller's FSSAI status, please visit https://foscos.fssai.gov.in/ and use the FBO search option with FSSAI License / Registration number.</li>
                <li>Please note that we never ask for bank account details such as CVV, account number, UPI Pin, etc. across our support channels. For your safety please do not share these details with anyone over any medium.</li>
                <li>MRP displayed on the platform is as printed on the product package. Actual MRP and amount payable may be a function of offers/ discounts and/ or the revised GST rates made effective by Govt. from 22 Sep 2025 onwards.</li>
            </ol>
        </div>
    </div>
</body>
</html>
