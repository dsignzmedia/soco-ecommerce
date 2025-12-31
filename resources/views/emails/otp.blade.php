<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<title>Your Login OTP - The Skool Store</title>

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
Login Verification
</h1>

<p style="margin:0 0 20px;color:#9a6c4c;">
Hello! Use the following One Time Password (OTP) to log in to your account. This OTP is valid for 60 seconds.
</p>

<!-- OTP BOX -->
<table width="100%" cellpadding="20" cellspacing="0" style="background:#fcfaf8;border:2px dashed #490D59;border-radius:10px;margin-bottom:20px;">
<tr>
<td align="center">
<p style="margin:0;color:#490D59;font-size:12px;font-weight:bold;text-transform:uppercase;">
Your OTP Code
</p>
<p style="margin:10px 0;font-size:48px;font-weight:900;color:#490D59;letter-spacing:8px;">
{{ $otp }}
</p>
</td>
</tr>
</table>

<p style="margin:0 0 20px;color:#9a6c4c;font-size:13px;">
If you didn't request this code, please ignore this email.
</p>

<!-- SUPPORT BOX -->
<table width="100%" cellpadding="16" cellspacing="0" style="width:100%;max-width:100%;background:#fcfaf8;border-radius:10px;margin-top:24px;">
<tr>
<td align="center" style="text-align:center;">
<p style="font-weight:bold;margin:0 0 6px;">Need help?</p>
<p style="margin:0 0 10px;color:#9a6c4c;">If you're having trouble logging in, contact our support team.</p>
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
