<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<title>Return/Exchange Request - The Skool Store</title>

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
Request #{{ $returnRequest->id }}
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
@if($status === 'submitted')
Return/Exchange Request Submitted
@elseif($status === 'approved')
Return/Exchange Request Approved
@elseif($status === 'rejected')
Return/Exchange Request Rejected
@elseif($status === 'received')
Return Item Received
@else
Return/Exchange Request Update
@endif
</h1>

<p style="margin:0 0 20px;color:#9a6c4c;">
@if($status === 'submitted')
Hello {{ $returnRequest->order->customer_name }}, we've received your {{ $returnRequest->type }} request. Our team will review it and get back to you soon.
@elseif($status === 'approved')
Great news, {{ $returnRequest->order->customer_name }}! Your {{ $returnRequest->type }} request has been approved.
@elseif($status === 'rejected')
Hello {{ $returnRequest->order->customer_name }}, we're sorry, but your {{ $returnRequest->type }} request could not be approved at this time.
@elseif($status === 'received')
Hello {{ $returnRequest->order->customer_name }}, we've received your returned item. We're processing it now.
@else
Hello {{ $returnRequest->order->customer_name }}, your {{ $returnRequest->type }} request has been updated.
@endif
</p>

<!-- STATUS BOX -->
<table width="100%" cellpadding="12" cellspacing="0" style="background:#fcfaf8;border:1px solid #eee;border-radius:10px;margin-bottom:20px;">
<tr><td>

<p style="margin:0;color:#490D59;font-size:12px;font-weight:bold;text-transform:uppercase;">
Request Status
</p>

<p style="margin:5px 0 10px;font-size:26px;font-weight:900;color:{{ $returnRequest->status === 'approved' ? '#28a745' : ($returnRequest->status === 'rejected' ? '#dc3545' : '#490D59') }};">
{{ ucfirst(str_replace('_', ' ', $returnRequest->status)) }}
</p>

<p style="margin:0;font-size:13px;color:#555;">
Order #{{ $returnRequest->order->order_number }}
</p>

</td></tr>
</table>

<!-- REQUEST INFO -->
<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;" class="two-column">
<tr>

<td width="50%" valign="top" style="padding-right:10px;width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Request Details</p>
<p style="margin:0;font-weight:bold;">{{ $returnRequest->order->item_name }}</p>
<p style="margin:0;">Type: {{ ucfirst($returnRequest->type) }}</p>
@if($returnRequest->reason)
<p style="margin:0;">Reason: {{ $returnRequest->reason }}</p>
@endif
</td>

<td width="50%" valign="top" style="width:50%;" class="two-column">
<p style="font-size:11px;color:#9a6c4c;font-weight:bold;text-transform:uppercase;">Order Information</p>
<p style="margin:0;font-weight:bold;">Order #{{ $returnRequest->order->order_number }}</p>
<p style="margin:0;">Request ID: #{{ $returnRequest->id }}</p>
</td>

</tr>
</table>

@if($status === 'approved' && $returnRequest->type === 'return')
<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="background:#fcfaf8;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="font-weight:bold;margin:0 0 8px;color:#490D59;">Next Steps:</p>
<p style="margin:0;font-size:13px;color:#555;">Please return the item to us. You'll receive further instructions via email or you can check your account for return shipping details.</p>
</td>
</tr>
</table>
@elseif($status === 'approved' && $returnRequest->type === 'exchange')
<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="background:#fcfaf8;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="font-weight:bold;margin:0 0 8px;color:#490D59;">Next Steps:</p>
<p style="margin:0;font-size:13px;color:#555;">Please return the item to us. Once we receive it, we'll process your exchange order.</p>
</td>
</tr>
</table>
@elseif($status === 'rejected' && $returnRequest->admin_notes)
<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="background:#fcfaf8;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="font-weight:bold;margin:0 0 8px;color:#490D59;">Admin Notes:</p>
<p style="margin:0;font-size:13px;color:#555;">{{ $returnRequest->admin_notes }}</p>
<p style="margin:8px 0 0;font-size:13px;color:#555;">If you have any questions or concerns, please contact our customer support team.</p>
</td>
</tr>
</table>
@elseif($status === 'received')
<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="background:#fcfaf8;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="margin:0;font-size:13px;color:#555;">Your returned item has been received and processed. @if($returnRequest->type === 'return') We'll process your refund shortly. @else We'll process your exchange order shortly. @endif</p>
</td>
</tr>
</table>
@endif

@if($returnRequest->admin_notes && $status !== 'rejected')
<!-- INFO BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="background:#fcfaf8;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="font-weight:bold;margin:0 0 8px;color:#490D59;">Admin Notes:</p>
<p style="margin:0;font-size:13px;color:#555;">{{ $returnRequest->admin_notes }}</p>
</td>
</tr>
</table>
@endif

<!-- SUPPORT BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#fcfaf8;border-radius:10px;margin-top:24px;">
<tr>
<td align="center" style="text-align:center;">
<p style="font-weight:bold;margin:0 0 6px;">Have a question?</p>
<p style="margin:0 0 10px;color:#9a6c4c;">We're here to help if you have any issues with your request.</p>
<a href="mailto:support@theskoolstore.com" style="color:#490D59;text-decoration:none;font-weight:bold;">Contact Support</a>
&nbsp; | &nbsp;
<a href="{{ route('frontend.parent.orders') }}" style="color:#490D59;text-decoration:none;font-weight:bold;">View Orders</a>
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
