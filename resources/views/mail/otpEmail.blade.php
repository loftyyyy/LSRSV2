<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Recovery OTP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f8; padding: 20px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 6px; overflow: hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color: #111827; padding: 20px; text-align: center;">
                        <h1 style="color: #ffffff; margin: 0; font-size: 20px;">
                            {{ config('app.name') }}
                        </h1>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding: 30px; color: #333333;">
                        <p style="margin-top: 0; font-size: 14px;">
                            Hello <strong>{{ $user->name }}</strong>,
                        </p>

                        <p style="font-size: 14px; line-height: 1.6;">
                            We received a request to reset the password for your account.
                        </p>

                        <p style="font-size: 14px; line-height: 1.6;">
                            Please use the One-Time Password (OTP) below to continue:
                        </p>

                        <!-- OTP Box -->
                        <div style="text-align: center; margin: 30px 0;">
                            <span style="
                                display: inline-block;
                                padding: 15px 30px;
                                font-size: 22px;
                                letter-spacing: 4px;
                                font-weight: bold;
                                color: #111827;
                                background-color: #f3f4f6;
                                border-radius: 6px;
                            ">
                                {{ $otp }}
                            </span>
                        </div>

                        <p style="font-size: 14px; line-height: 1.6;">
                            This OTP is valid for <strong>{{ $expiryMinutes }} minutes</strong>.
                            For your security, do not share this code with anyone.
                        </p>

                        <p style="font-size: 14px; line-height: 1.6;">
                            If you did not request a password reset, you may safely ignore this email.
                        </p>

                        <p style="font-size: 14px; margin-bottom: 0;">
                            Best regards,<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                        {{ config('mail.from.address') }}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
