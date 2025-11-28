<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Kata Sandi</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background: white; padding: 30px; border-radius: 10px;">
                    <tr>
                        <td align="center">
                            <img src="https://dalitmayaan.com/logo.png" width="80" alt="logo">
                            <h2 style="margin-top: 10px; color: #004aad;">HELPDESK POLINDRA</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top: 20px; font-size: 15px;">
                            <p>Halo {{ $user->name ?? 'Pengguna' }},</p>

                            <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.</p>

                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}" 
                                   style="background: #004aad; padding: 12px 25px; color: white; 
                                          text-decoration: none; border-radius: 6px;">
                                   Reset Kata Sandi
                                </a>
                            </p>

                            <p>Link reset kata sandi ini akan berlaku selama <strong>60 menit</strong>.</p>

                            <p>Jika Anda tidak meminta reset kata sandi, abaikan saja email ini.</p>

                            <br>

                            <p>Hormat kami,<br>
                            <strong>Helpdesk Polindra</strong></p>
                        </td>
                    </tr>

                </table>

                <p style="margin-top: 20px; color: #777;">
                    © {{ date('Y') }} Helpdesk Polindra. All rights reserved.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>
