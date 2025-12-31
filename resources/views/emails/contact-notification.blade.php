<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<title>New Contact Form Submission - The Skool Store</title>

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
New Contact Form Submission
</h1>

<p style="margin:0 0 20px;color:#9a6c4c;">
You have received a new contact form submission from your website.
</p>

<!-- CONTACT DETAILS BOX -->
<table width="100%" cellpadding="20" cellspacing="0" style="background:#fcfaf8;border:2px solid #490D59;border-radius:10px;margin-bottom:20px;">
<tr>
<td>
<p style="margin:0 0 15px;color:#490D59;font-size:16px;font-weight:bold;">
Contact Information:
</p>

<table width="100%" cellpadding="8" cellspacing="0">
@if($contact->firstname || $contact->lastname)
<tr>
<td style="padding:8px 0;border-bottom:1px solid #eee;">
<strong style="color:#1b130d;">Name:</strong>
<span style="color:#9a6c4c;margin-left:10px;">{{ trim(($contact->firstname ?? '') . ' ' . ($contact->lastname ?? '')) }}</span>
</td>
</tr>
@endif

@if($contact->email)
<tr>
<td style="padding:8px 0;border-bottom:1px solid #eee;">
<strong style="color:#1b130d;">Email:</strong>
<span style="color:#9a6c4c;margin-left:10px;"><a href="mailto:{{ $contact->email }}" style="color:#490D59;text-decoration:none;">{{ $contact->email }}</a></span>
</td>
</tr>
@endif

@if($contact->phone)
<tr>
<td style="padding:8px 0;border-bottom:1px solid #eee;">
<strong style="color:#1b130d;">Phone:</strong>
<span style="color:#9a6c4c;margin-left:10px;"><a href="tel:{{ $contact->phone }}" style="color:#490D59;text-decoration:none;">{{ $contact->phone }}</a></span>
</td>
</tr>
@endif

@if($contact->message)
<tr>
<td style="padding:8px 0;">
<strong style="color:#1b130d;">Message:</strong>
<span style="color:#9a6c4c;margin-left:10px;line-height:1.6;white-space:pre-wrap;">{{ $contact->message }}</span>
</td>
</tr>
@endif
</table>

<p style="margin:15px 0 0;color:#9a6c4c;font-size:12px;">
Submitted on: {{ $contact->created_at->setTimezone('Asia/Kolkata')->format('F d, Y \a\t h:i A') }}
</p>

</td>
</tr>
</table>

<p style="margin:0 0 20px;color:#9a6c4c;font-size:13px;">
Please respond to this inquiry as soon as possible.
</p>

</td>
</tr>
</table>

<!-- FOOTER -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#1b130d;color:#9a6c4c;">
<tr>
<td align="center" style="text-align:center;padding:16px;">

<p style="margin:0 0 6px;">© {{ date('Y') }} The Skool Store. All rights reserved.</p>
<p style="margin:0 0 6px;">The Skool Store, India</p>

</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>

