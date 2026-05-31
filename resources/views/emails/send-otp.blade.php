<!DOCTYPE html>
<html lang="id" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Kode OTP Fundlink</title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            padding: 40px 16px;
            background-color: #f1f5f9;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            padding: 36px 40px;
            text-align: center;
        }
        .header-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }
        .header-logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.03em;
        }
        .header p {
            color: #bfdbfe;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 2px;
        }
        /* Body */
        .body {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .description {
            font-size: 14px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        /* OTP Box */
        .otp-container {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            margin-bottom: 28px;
        }
        .otp-label {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 12px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 14px;
            color: #1d4ed8;
            font-family: 'Courier New', Courier, monospace;
            line-height: 1;
            padding-left: 14px; /* compensate letter-spacing on last char */
        }
        .otp-expiry {
            margin-top: 12px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }
        .otp-expiry strong {
            color: #f59e0b;
            font-weight: 700;
        }
        /* Warning */
        .warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
            margin-bottom: 28px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .warning-icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 28px 0;
        }
        .ignore-note {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
        }
        /* Footer */
        .footer {
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 24px 40px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .footer strong {
            color: #64748b;
        }

        @media (max-width: 600px) {
            .body { padding: 28px 24px; }
            .footer { padding: 20px 24px; }
            .otp-code { font-size: 36px; letter-spacing: 10px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">

            <!-- Header -->
            <div class="header">
                <div class="header-logo">
                    <h1>Fundlink</h1>
                </div>
                <p>Sistem Manajemen Keuangan Yayasan</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">Halo, {{ $userName }} 👋</p>
                <p class="description">
                    Kami menerima permintaan verifikasi untuk akun Anda di <strong>Fundlink</strong>.
                    Gunakan kode OTP di bawah ini untuk menyelesaikan proses verifikasi.
                </p>

                <!-- OTP Code Box -->
                <div class="otp-container">
                    <p class="otp-label">Kode Verifikasi OTP</p>
                    <p class="otp-code">{{ $otpCode }}</p>
                    <p class="otp-expiry">
                        Berlaku selama <strong>{{ $expiresInMinutes }} menit</strong> sejak email ini dikirim
                    </p>
                </div>

                <!-- Security Warning -->
                <div class="warning">
                    <span class="warning-icon">⚠️</span>
                    <span>
                        <strong>Jaga kerahasiaan kode ini.</strong>
                        Tim Fundlink tidak akan pernah meminta kode OTP Anda melalui telepon, chat, atau media lainnya.
                    </span>
                </div>

                <hr class="divider">

                <p class="ignore-note">
                    Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.
                    Akun Anda tetap aman dan tidak ada perubahan yang terjadi.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    Email ini dikirim secara otomatis oleh sistem <strong>Fundlink</strong>.<br>
                    Mohon jangan membalas email ini.
                </p>
                <p style="margin-top: 8px;">
                    &copy; {{ date('Y') }} Fundlink. Semua hak dilindungi.
                </p>
            </div>

        </div>
    </div>
</body>
</html>
