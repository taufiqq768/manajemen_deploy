<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#1e293b;-webkit-font-smoothing:antialiased;">

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f1f5f9;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;">

          {{-- ─── Header Logo ──────────────────────────────── --}}
          <tr>
            <td style="padding-bottom:24px;text-align:center;">
              <table cellpadding="0" cellspacing="0" role="presentation" style="margin:0 auto;">
                <tr>
                  <td style="background:linear-gradient(135deg,#6366f1,#9333ea);width:44px;height:44px;border-radius:12px;text-align:center;vertical-align:middle;">
                    <span style="font-size:22px;line-height:44px;display:block;">📦</span>
                  </td>
                  <td style="padding-left:12px;vertical-align:middle;text-align:left;">
                    <p style="margin:0;font-size:16px;font-weight:700;color:#1e293b;line-height:1.2;">{{ $appName }}</p>
                    <p style="margin:0;font-size:11px;color:#64748b;margin-top:2px;">Production Release System</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- ─── Card ───────────────────────────────────── --}}
          <tr>
            <td style="background:#ffffff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">

              {{-- Banner --}}
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="background:{{ $bannerColor }};padding:28px 32px;text-align:center;">
                    <p style="margin:0 0 8px 0;font-size:36px;">{{ $bannerIcon }}</p>
                    <h1 style="margin:0;font-size:20px;font-weight:700;color:#ffffff;line-height:1.3;">
                      {{ $title }}
                    </h1>
                  </td>
                </tr>
              </table>

              {{-- Body --}}
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="padding:32px;">

                    {{-- Salam --}}
                    <p style="margin:0 0 20px 0;font-size:15px;color:#475569;">
                      Halo, <strong style="color:#1e293b;">{{ $recipientName }}</strong> 👋
                    </p>

                    {{-- Pesan utama --}}
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td style="background:#f8fafc;border-left:4px solid {{ $bannerColor }};border-radius:0 8px 8px 0;padding:16px 20px;">
                          <p style="margin:0;font-size:14px;color:#334155;line-height:1.7;">
                            {{ $body }}
                          </p>
                        </td>
                      </tr>
                    </table>

                    {{-- Keterangan tambahan --}}
                    @if($detail)
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:16px;">
                      <tr>
                        <td style="background:#f1f5f9;border-radius:8px;padding:14px 18px;">
                          <p style="margin:0 0 6px 0;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">
                            Keterangan
                          </p>
                          <p style="margin:0;font-size:14px;color:#475569;line-height:1.6;">
                            {{ $detail }}
                          </p>
                        </td>
                      </tr>
                    </table>
                    @endif

                    {{-- Tombol CTA --}}
                    @if($detailUrl)
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:28px;">
                      <tr>
                        <td align="center">
                          <a href="{{ $detailUrl }}"
                             style="display:inline-block;background:{{ $bannerColor }};color:#ffffff;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;text-decoration:none;letter-spacing:0.01em;">
                            Lihat Detail Request &rarr;
                          </a>
                        </td>
                      </tr>
                    </table>
                    @endif

                  </td>
                </tr>
              </table>

              {{-- Divider --}}
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="padding:0 32px;">
                    <hr style="border:none;border-top:1px solid #e2e8f0;margin:0;">
                  </td>
                </tr>
              </table>

              {{-- Footer card --}}
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="padding:20px 32px;">
                    <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                      Email ini dikirim secara otomatis oleh sistem
                      <strong style="color:#64748b;">{{ $appName }}</strong>.
                      Mohon tidak membalas email ini. Jika ada pertanyaan, hubungi tim IT.
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          {{-- ─── Footer luar ─────────────────────────────── --}}
          <tr>
            <td style="padding-top:24px;text-align:center;">
              <p style="margin:0;font-size:11px;color:#94a3b8;">
                &copy; {{ date('Y') }} {{ $appName }} &mdash; Production Release System<br>
                <a href="{{ $appUrl }}" style="color:#6366f1;text-decoration:underline;">{{ $appUrl }}</a>
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
