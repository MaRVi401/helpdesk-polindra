<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body
  style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 40px 20px;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center">
        <!-- MAIN CONTAINER -->
        <table width="600" cellpadding="0" cellspacing="0" border="0"
          style="max-width: 600px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
          <!-- HEADER WITH LOGO -->
          <tr>
            <td style="padding: 50px 50px 30px; text-align: center;">
              <img src="https://pub-b5d114e52b2949f283554f792e009c90.r2.dev/LogoServiceDesk.png"
                width="200" alt="Logo Servicedesk" style="display: block; margin: 0 auto;">
            </td>
          </tr>
          <!-- MAIN CONTENT -->
          <tr>
            <td style="padding: 0 50px 50px;">
              <!-- MESSAGE -->
              <p style="margin: 0 0 25px; color: #666; font-size: 15px; line-height: 1.7;">
                Kami menerima permintaan untuk melakukan reset password pada akun Kamu.
                Silakan klik tombol/tautan di bawah ini untuk mengatur ulang password.
              </p>
              <!-- CTA BUTTON -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ $url }}"
                      style="display: inline-block; background: #004aad; color: white; text-decoration: none; padding: 16px 50px; border-radius: 8px; font-weight: 600; font-size: 16px;">
                      Reset Password
                    </a>
                  </td>
                </tr>
              </table>
              <!-- ALTERNATIVE LINK -->
              <p style="margin: 30px 0 0; font-size: 13px; color: #999; line-height: 1.6; text-align: center;">
                Atau copy dan paste link berikut ke browser kamu:
              </p>
              <div
                style="background: #f8f8f8; border-radius: 6px; padding: 12px; margin: 10px 0 30px; word-break: break-all; text-align: center;">
                <a href="{{ $url }}"
                  style="color: #004aad; font-size: 12px; text-decoration: none;">{{ $url }}</a>
              </div>
              <!-- SIGNATURE -->
              <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e5e5e5;">
                <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.8;">
                  Hormat kami,<br>
                  <strong style="color: #333;">Service Desk Politeknik Negeri Indramayu</strong>
                </p>
              </div>
            </td>
          </tr>
          <!-- FOOTER -->
          <tr>
            <td style="background: #f8f8f8; padding: 30px 50px; text-align: center; border-top: 1px solid #e5e5e5;">
              <p style="margin: 0; color: #999; font-size: 12px; line-height: 1.8;">
                Jl. Raya Lohbener Lama No. 08, Kec. Lohbener<br>
                Kab. Indramayu, Jawa Barat 45252<br><br>
                © {{ date('Y') }} Polindra. All Rights Reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>
