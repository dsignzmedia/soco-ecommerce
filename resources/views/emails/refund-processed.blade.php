<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<title>Refund Processed - The Skool Store</title>

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
Refund Processed
</h1>

<p style="margin:0 0 20px;color:#9a6c4c;">
Hello {{ $order->customer_name }}, your refund has been successfully processed.
</p>

<!-- SUCCESS BOX -->
<table width="100%" cellpadding="12" cellspacing="0" style="background:#d4edda;border:2px solid #28a745;border-radius:10px;margin-bottom:20px;">
<tr><td align="center">

<p style="margin:0;font-size:48px;">✓</p>
<p style="margin:10px 0;font-size:20px;font-weight:700;color:#28a745;">
Refund Processed Successfully
</p>

</td></tr>
</table>

<!-- REFUND INFO -->
<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;" class="two-column">
<tr>

<td width="50%" valign="top" style="padding-right:10px;width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Refund Details</p>
<p style="margin:0;font-weight:bold;">{{ $order->item_name }}</p>
@if($refundId)
<p style="margin:0;">Refund ID: {{ $refundId }}</p>
@endif
</td>

<td width="50%" valign="top" style="width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Original Payment</p>
<p style="margin:0;font-weight:bold;">{{ $order->payment_method ? ucfirst($order->payment_method) : 'Online' }}</p>
<p style="margin:0;">Order #{{ $order->order_number }}</p>
</td>

</tr>
</table>

<!-- TOTALS -->
<hr style="border:0;border-top:1px solid #eee;margin:20px 0;">

<table width="100%" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
<tr>
<td style="padding-top:10px;font-weight:bold;width:50%;">Refund Amount</td>
<td align="right" style="color:#28a745;font-weight:bold;font-size:20px;padding-top:10px;width:50%;text-align:right;">₹{{ number_format($refundAmount, 2) }}</td>
</tr>
</table>

<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#fcfaf8;border-radius:10px;margin-top:20px;">
<tr>
<td style="width:100%;">
<p style="font-weight:bold;margin:0 0 8px;color:#490D59;">Important Information:</p>
<p style="margin:0 0 8px;font-size:13px;color:#555;"><strong>Processing Time:</strong> The refund has been processed and will be credited back to your original payment method.</p>
<p style="margin:0 0 8px;font-size:13px;color:#555;"><strong>Credit Timeline:</strong> You should see the refund in your account within 5-7 business days, depending on your bank or payment provider.</p>
<p style="margin:0;font-size:13px;color:#555;"><strong>If you don't see the refund:</strong> Please wait for the full processing period. If you still don't see it after 7 business days, please contact us with your refund transaction ID.</p>
</td>
</tr>
</table>

<!-- SUPPORT BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#fcfaf8;border-radius:10px;margin-top:24px;">
<tr>
<td align="center" style="text-align:center;">
<p style="font-weight:bold;margin:0 0 6px;">Have a question?</p>
<p style="margin:0 0 10px;color:#9a6c4c;">If you have any questions about this refund, please contact us.</p>
<a href="mailto:support@theskoolstore.com" style="color:#490D59;text-decoration:none;font-weight:bold;">Contact Support</a>
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
