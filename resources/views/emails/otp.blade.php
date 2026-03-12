<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cyber Majlis OTP</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f8; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0; background-color:#f3f4f8;">
<tr>
<td align="center">

<table width="520" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden;">

    <!-- Top Purple Bar -->
    <tr>
        <td style="background:#6f42c1; height:6px;"></td>
    </tr>

    <!-- Logo -->
    <tr>
        <td align="center" style="padding:30px 20px 10px 20px;">
            <img src="{{env('MEDIA_BASE_URL')}}/frontend/assets/images/email-logo.jpg"  
                 alt="Cyber Majlis"
                 style="display:block; border:0;">
        </td>
    </tr>

    <!-- Heading -->
    <tr>
        <td align="center" style="padding:10px 40px;">
            <h2 style="margin:0; font-size:22px; color:#2b2b2b;">
                Cyber Majlis One-time Code
            </h2>
        </td>
    </tr>

    <!-- Message -->
    <tr>
        <td align="center" style="padding:15px 40px; color:#555; font-size:15px; line-height:22px;">
            Use the following code to sign in to the Members’ Portal:
        </td>
    </tr>

    <!-- OTP BOX -->
    <tr>
        <td align="center" style="padding:10px 40px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center"
                        style="
                            background-color:#6f42c1;
                            color:#ffffff;
                            font-size:34px;
                            font-weight:bold;
                            letter-spacing:8px;
                            padding:20px 0;
                            border-radius:12px;
                        ">
                        {{ $code }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Expiry -->
    <tr>
        <td align="center" style="padding:25px 40px 10px 40px; font-size:13px; color:#777; line-height:20px;">
            This code is valid for 10 minutes.<br>
            If you did not request it, you can ignore this email.
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td align="center" style="padding:25px 20px; font-size:12px; color:#aaa;">
            © {{ date('Y') }} Cyber Majlis. All rights reserved.
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
