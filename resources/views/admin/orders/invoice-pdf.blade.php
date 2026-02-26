<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tax Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .outer-border {
            border: 0.75pt solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 0.5pt solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .header-table td {
            border-bottom: 0.75pt solid #000;
        }
        .info-table td {
            width: 50%;
            border-bottom: 0.75pt solid #000;
        }
        .item-table th {
            background: #ededed;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        
        .logo-box {
            padding: 10px;
            text-align: center;
        }
        .logo-styled {
            display: inline-block;
            border: 2pt solid #490d59;
            padding: 5pt 10pt;
            color: #490d59;
            font-weight: bold;
            font-size: 20pt;
            letter-spacing: 2pt;
        }
        .summary-table {
            width: 250pt;
            float: right;
            margin: 10pt;
        }
        .summary-table td {
            border: 0.5pt solid #000;
        }
        .grand-total {
            background: #ededed;
            font-size: 12pt;
            font-weight: bold;
        }
        .footer-table td {
            border: none;
            border-top: 0.75pt solid #000;
            padding-top: 20pt;
        }
        .signature-box {
            border: 0.5pt solid #000;
            padding: 10pt;
            text-align: center;
            width: 150pt;
            float: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        @php
            $branding = \App\Models\Admin\Master\AppBranding::current();
            $hasGD = extension_loaded('gd');
            
            // Only try to use logo if GD is enabled
            $logoBase64 = '';
            if ($hasGD) {
                $logoPath = public_path('assets/img/soco_logo/logo_r_soco.png');
                if (file_exists($logoPath)) {
                    try {
                        $logoData = base64_encode(file_get_contents($logoPath));
                        $logoBase64 = 'data:image/png;base64,' . $logoData;
                    } catch (\Exception $e) {}
                }
            }

            $totalTaxable = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $totalShipping = 0;
            $grandTotal = 0;
            $totalQty = 0;
            
            // Robust fallback: if $orders is missing, empty, or not a collection, use the single $order
            if (!isset($orders) || (is_countable($orders) && count($orders) === 0)) {
                $orders = collect([$order]);
            }
            
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
            $currency = 'Rs. '; // Safe alternative for PDF
        @endphp

        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="height: 40pt; width: auto;" alt="Logo">
                    @else
                        <div class="logo-styled">SOCO</div>
                    @endif
                    <div style="margin-top: 10pt; font-size: 9pt;">
                        <span class="bold">{{ $branding->seller_name }}</span><br>
                        {{ $branding->seller_address }}
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div style="font-size: 20pt; font-weight: bold;">TAX INVOICE</div>
                    <div style="margin-top: 10pt;">
                        Invoice No: <span class="bold">{{ $order->order_number }}</span><br>
                        Date: <span class="bold">{{ now()->format('d-m-Y') }}</span><br>
                        Status: <span class="bold">{{ strtoupper($order->payment_status) }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>
                    <div style="font-size: 8pt; color: #666; margin-bottom: 5pt; text-transform: uppercase;">Seller Details</div>
                    <span class="bold">{{ $branding->seller_name }}</span><br>
                    GSTIN: {{ $branding->seller_gstin }}<br>
                    PAN: {{ $branding->seller_pan }}<br>
                    FSSAI: {{ $branding->seller_fssai }}
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 8pt; color: #666; margin-bottom: 5pt; text-transform: uppercase;">Billed To</div>
                    <span class="bold">{{ $order->customer_name }}</span><br>
                    {{ $order->customer_address }}<br>
                    Pincode: {{ $order->pincode ?? '—' }}<br>
                    State: Tamil Nadu
                </td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 25pt;">Sl.</th>
                    <th>Product Description / HSN</th>
                    <th style="width: 30pt;">Qty</th>
                    <th style="width: 65pt;">Price</th>
                    <th style="width: 65pt;">Taxable</th>
                    <th style="width: 60pt;">Tax</th>
                    <th style="width: 75pt;">Total</th>
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
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <span class="bold">{{ $o->item_name }}</span><br>
                            <span style="font-size: 7pt; color: #444;">HSN: {{ $o->product ? $o->product->hsn_code : '—' }}</span>
                        </td>
                        <td class="text-center">{{ $o->quantity }}</td>
                        <td class="text-right">{{ $currency }}{{ number_format(($itemTaxable + $itemTax) / $o->quantity, 2) }}</td>
                        <td class="text-right">{{ $currency }}{{ number_format($itemTaxable, 2) }}</td>
                        <td class="text-right">{{ $currency }}{{ number_format($itemTax, 2) }}</td>
                        <td class="text-right bold">{{ $currency }}{{ number_format($itemTaxable + $itemTax, 2) }}</td>
                    </tr>
                @endforeach
                @if($totalShipping > 0)
                <tr>
                    <td class="text-center">{{ count($orders) + 1 }}</td>
                    <td>Shipping & Handling Charges</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ $currency }}{{ number_format($totalShipping, 2) }}</td>
                    <td class="text-right">{{ $currency }}{{ number_format($totalShipping, 2) }}</td>
                    <td class="text-right">{{ $currency }}0.00</td>
                    <td class="text-right bold">{{ $currency }}{{ number_format($totalShipping, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div style="border-bottom: 1.5pt solid #000; padding: 10pt;">
            <div style="font-size: 8pt; color: #666; text-transform: uppercase;">Amount in Words:</div>
            <div style="font-size: 10pt; font-weight: bold; margin-top: 3pt;">{{ $amountWords }}</div>
        </div>

        <div class="clearfix">
            <table class="summary-table">
                <tr>
                    <td class="bold">Taxable Subtotal</td>
                    <td class="text-right">{{ $currency }}{{ number_format($totalTaxable + $totalShipping, 2) }}</td>
                </tr>
                @if($isInterState)
                    <tr>
                        <td class="bold">IGST</td>
                        <td class="text-right">{{ $currency }}{{ number_format($totalIgst, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="bold">CGST</td>
                        <td class="text-right">{{ $currency }}{{ number_format($totalCgst, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="bold">SGST</td>
                        <td class="text-right">{{ $currency }}{{ number_format($totalSgst, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>GRAND TOTAL</td>
                    <td class="text-right">{{ $currency }}{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="padding: 15pt; border-top: 1.5pt solid #000;" class="clearfix">
            <div style="width: 50%; float: left; font-size: 8pt;">
                <span class="bold">Declaration:</span><br>
                We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
            </div>
            <div class="signature-box">
                @if($branding->signature_path && $hasGD)
                    <img src="{{ public_path('storage/' . $branding->signature_path) }}" style="height: 35pt; width: auto; margin-bottom: 5pt;">
                @else
                    <div style="height: 35pt;"></div>
                @endif
                <div style="font-size: 9pt; font-weight: bold; border-top: 1pt solid #000; padding-top: 5pt;">Authorised Signatory</div>
                <div style="font-size: 8pt;">For {{ $branding->seller_name }}</div>
            </div>
        </div>
    </div>
</body>
</html>
