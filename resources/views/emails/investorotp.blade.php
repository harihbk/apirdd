<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Investor Password Reset</title>
    </head>
    <body>
        <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        <p>Kindly find below the OTP for your Password Reset Request.</p>
        <p>OTP: <b>{{ $otp }}</b></p><br/>
        <p>Regards,</p>
        <p>RDD Team</p>
    </body>
</html>