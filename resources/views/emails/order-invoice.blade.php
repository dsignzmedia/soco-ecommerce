<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello {{ $order->customer_name }},</h2>
    <p>Please find the Tax Invoice for your recent order <strong>#{{ $order->order_number }}</strong> attached to this email.</p>
    <p>Thank you for shopping with SoCo Products!</p>
    <br>
    <p>Best regards,<br>The Skool Store Team</p>
</body>
</html>
