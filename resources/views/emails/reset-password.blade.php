<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        {{-- CARD KONTEN --}}
        <table width="600" cellpadding="0" cellspacing="0"
          style="background: white; padding: 30px; border-radius: 10px;">
          {{-- HEADER --}}
          <tr>
            <td align="center" style="padding-bottom: 10px;">
              <img src="https://res.cloudinary.com/ddmmgr8gd/image/upload/service_desk_nkr98m.svg" width="200"
                alt="Logo Servicedesk">
            </td>
          </tr>
          {{-- BODY --}}
          <tr>
            <td style="padding-top: 20px; font-size: 15px; color: #333; line-height: 1.6;">
              <p>Halo <strong>{{ $user->name ?? 'Pengguna' }}</strong>,</p>
              <p>Kamu menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.</p>
              <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}"
                  style="background: #004aad; padding: 12px 25px; color: white; 
                    text-decoration: none; border-radius: 6px; display:inline-block;">
                  Reset Password
                </a>
              </p>
              <p>Link reset password ini akan berlaku selama <strong>60 menit</strong>.</p>
              <p>Jika Anda tidak meminta reset kata sandi, abaikan saja email ini.</p>
              <br>
              <p>Hormat kami,<br>
                <strong>Servicedesk Polindra</strong>
              </p>
              <hr style="margin: 30px 0; border: 0; border-top: 1px solid #e5e5e5;">
              <p style="font-size: 12px; color: #666;">
                Jika tombol tidak berfungsi, klik atau salin link berikut ke browser Kamu:<br>
                <a href="{{ $url }}" style="color:#004aad; word-break:break-all;">
                  {{ $url }}
                </a>
              </p>

            </td>
          </tr>
        </table>
        {{-- FOOTER --}}
        <table width="600" cellpadding="0" cellspacing="0" style="margin-top: 25px;">
          <tr>
            <td align="center" style="font-size: 12px; color:#666; font-family: Arial; line-height: 1.6;">
              <strong>SERVICE DESK POLITEKNIK NEGERI INDRAMAYU</strong><br>
              Jl. Raya Lohbener Lama No. 08, Kec. Lohbener, Kab. Indramayu, Jawa Barat 45252<br>
              © {{ date('Y') }} — All Rights Reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>
