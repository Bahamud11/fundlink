<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Fundlink</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; margin: 0; padding: 0; }
        .wrapper { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 900; margin: 0; letter-spacing: -0.03em; }
        .header p { color: #bfdbfe; font-size: 13px; margin: 4px 0 0; }
        .body { padding: 40px 32px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 16px; }
        .desc { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 32px; }
        .otp-box { background: #f1f5f9; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 32px; }
        .otp-code { font-size: 40px; font-weight: 900; letter-spacing: 12px; color: #1e40af; font-family: monospace; }
        .otp-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 8px; }
        .warning { background: #fef3c7; border-radius: 12px; padding: 16px; font-size: 13px; color: #92400e; margin-bottom: 24px; }
        .footer { padding: 24px 32px; border-top: 1px solid #f3f4f6; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Fundlink</h1>
            <p>Sistem Manajemen Keuangan Yayasan</p>
        </div>
        <div class="body">
            <p class="greeting">Halo, <strong>{{ $userName }}</strong> 👋</p>
            <p class="desc">
                Gunakan kode OTP berikut untuk memverifikasi akun Anda.
                Kode ini hanya berlaku selama <strong>10 menit</strong>.
            </p>

            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="otp-label">Kode Verifikasi OTP</div>
            </div>

            <div class="warning">
                ⚠️ Jangan bagikan kode ini kepada siapapun. Tim Fundlink tidak akan pernah meminta kode OTP Anda.
            </div>

            <p class="desc" style="margin-bottom: 0;">
                Jika Anda tidak merasa mendaftar, abaikan email ini.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Fundlink. Semua hak dilindungi.
        </div>
    </div>
</body>
</html>
