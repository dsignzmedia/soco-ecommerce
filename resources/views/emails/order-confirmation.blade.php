<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<title>Order Confirmation - The Skool Store</title>

<style>
/* CLIENT RESETS */
body { margin:0; padding:0; background:#f8f7f6; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
img { border:0; display:block; max-width:100%; height:auto; }
table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
table td { border-collapse:collapse; }

/* MOBILE */
@media only screen and (max-width:600px){
.wrapper { width:100% !important; max-width:100% !important; }
.pad { padding:16px !important; }
.h1 { font-size:26px !important; }
.two-column { width:100% !important; max-width:100% !important; display:block !important; }
.two-column td { width:100% !important; display:block !important; padding-bottom:10px !important; }
}
</style>
</head>

<body style="margin:0;padding:0;background:#f8f7f6;font-family:Arial,Helvetica,sans-serif;color:#1b130d;">

<table width="100%" cellpadding="0" cellspacing="0" align="center" style="background:#f8f7f6;">
<tr>
<td align="center" style="padding:20px 0;">

<!-- CARD -->
<table width="600" class="wrapper" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;min-width:320px;background:#ffffff;border-radius:10px;overflow:hidden;margin:0 auto;">
<tr>
<td>

<!-- HEADER -->
<table width="100%" cellpadding="0" cellspacing="0" style="width:100%;">
<tr>
<td style="padding:20px;border-bottom:1px solid #eee;">
<table width="100%" cellpadding="0" cellspacing="0" style="width:100%;">
<tr>
<td align="left" style="width:auto;">
<img src="https://dev-soco-ecommerce.back2skool.in/assets/img/new%20logo/new_logo.png" alt="The Skool Store" style="max-width:150px;width:150px;height:auto;display:block;">
</td>
<td align="right" style="font-size:13px;color:#9a6c4c;width:auto;text-align:right;">
Order #{{ $order->order_number }}
</td>
</tr>
</table>
</td>
</tr>
</table>

<!-- BODY -->
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="pad" style="padding:28px;">

<!-- TITLE -->
<h1 class="h1" style="margin:0 0 10px;font-size:30px;font-weight:800;">
Your order has been confirmed!
</h1>

<p style="margin:0 0 20px;color:#9a6c4c;">
Great news, {{ $order->customer_name }}! We've received your order and it's being processed.
</p>

<!-- ORDER BOX -->
<table width="100%" cellpadding="12" cellspacing="0" style="background:#fcfaf8;border:1px solid #eee;border-radius:10px;margin-bottom:20px;">
<tr><td>

<p style="margin:0;color:#490D59;font-size:12px;font-weight:bold;text-transform:uppercase;">
Order Status
</p>

<p style="margin:5px 0 10px;font-size:26px;font-weight:900;color:#490D59;">
{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
</p>

<p style="margin:0;font-size:13px;color:#555;">
Order Date: {{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}
</p>

<!-- BUTTON -->
<table cellpadding="0" cellspacing="0" width="100%" style="margin-top:12px;">
<tr>
<td align="center">
<a href="{{ route('frontend.parent.orders') }}" style="display:block;background:#490D59;color:#ffffff;font-weight:bold;padding:12px;border-radius:6px;text-decoration:none;">
View Order
</a>
</td>
</tr>
</table>

</td></tr>
</table>

<!-- ORDER INFO -->
<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;" class="two-column">
<tr>

<td width="50%" valign="top" style="padding-right:10px;width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Customer</p>
<p style="margin:0;font-weight:bold;">{{ $order->customer_name }}</p>
<p style="margin:0;">{{ $order->customer_phone }}</p>
<p style="margin:0;">{{ $order->customer_email }}</p>
</td>

<td width="50%" valign="top" style="width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Payment Status</p>
<p style="margin:0;font-weight:bold;color:{{ $order->payment_status === 'paid' ? '#28a745' : '#ffc107' }};">{{ strtoupper($order->payment_status) }}</p>
<p style="margin:0;">{{ $order->payment_method ? ucfirst($order->payment_method) : 'N/A' }}</p>
</td>

</tr>
</table>

<!-- ITEMS -->
<hr style="border:0;border-top:1px solid #eee;margin:20px 0;">

<p style="font-size:16px;font-weight:bold;margin-bottom:12px;">
Order Items
</p>

<!-- ITEM -->
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
<tr>
<td width="80" style="background:#f5f5f5;border-radius:8px;text-align:center;vertical-align:middle;height:80px;width:80px;min-width:80px;">
<span style="font-size:32px;">📦</span>
</td>

<td valign="top" style="padding-left:10px;width:auto;">
<p style="margin:0;font-weight:bold;">{{ $order->item_name }}</p>
@if($order->size)
<p style="margin:2px 0;color:#9a6c4c;">Size: {{ $order->size }}</p>
@endif
@if($order->student_name)
<p style="margin:2px 0;color:#9a6c4c;">Student: {{ $order->student_name }}</p>
@endif
@if($order->grade)
<p style="margin:2px 0;color:#9a6c4c;">Grade: {{ $order->grade }}</p>
@endif
<p style="margin:0;color:#9a6c4c;">Qty: {{ $order->quantity }}</p>
</td>

<td valign="top" align="right" style="font-weight:bold;width:100px;text-align:right;">
₹{{ number_format($order->total_amount, 2) }}
</td>
</tr>
</table>

<!-- TOTALS -->
<hr style="border:0;border-top:1px solid #eee;margin:20px 0;">

<table width="100%" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
<tr>
<td style="width:50%;">Subtotal</td>
<td align="right" style="width:50%;text-align:right;">₹{{ number_format($order->total_amount - $order->tax_amount, 2) }}</td>
</tr>
@if($order->tax_amount > 0)
<tr>
<td style="width:50%;">Tax</td>
<td align="right" style="width:50%;text-align:right;">₹{{ number_format($order->tax_amount, 2) }}</td>
</tr>
@endif
@if($order->shipping_cost > 0)
<tr>
<td style="width:50%;">Shipping</td>
<td align="right" style="width:50%;text-align:right;">₹{{ number_format($order->shipping_cost, 2) }}</td>
</tr>
@else
<tr>
<td style="width:50%;">Shipping</td>
<td align="right" style="width:50%;text-align:right;">Free</td>
</tr>
@endif
<tr>
<td style="padding-top:10px;font-weight:bold;width:50%;">Total Paid</td>
<td align="right" style="color:#490D59;font-weight:bold;padding-top:10px;width:50%;text-align:right;">₹{{ number_format($order->total_amount, 2) }}</td>
</tr>
</table>

<!-- SUPPORT BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#fcfaf8;border-radius:10px;margin-top:24px;">
<tr>
<td align="center" style="text-align:center;">
<p style="font-weight:bold;margin:0 0 6px;">Have a question?</p>
<p style="margin:0 0 10px;color:#9a6c4c;">We're here to help if you have any issues with your order.</p>
<a href="mailto:support@theskoolstore.com" style="color:#490D59;text-decoration:none;font-weight:bold;">Contact Support</a>
&nbsp; | &nbsp;
<a href="#" style="color:#490D59;text-decoration:none;font-weight:bold;">Track Order</a>
</td>
</tr>
</table>

</td>
</tr>
</table>

<!-- FOOTER -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#1b130d;color:#9a6c4c;">
<tr>
<td align="center" style="text-align:center;padding:16px;">

<p style="margin:0 0 6px;">© {{ date('Y') }} The Skool Store. All rights reserved.</p>
<p style="margin:0 0 6px;">The Skool Store, India</p>

<a href="#" style="color:#ffffff;text-decoration:underline;">Unsubscribe</a>
&nbsp; • &nbsp;
<a href="#" style="color:#ffffff;text-decoration:underline;">Privacy Policy</a>

</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>
