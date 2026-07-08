@php
  $nick = $user->nickname ?? $user->name ?? '러너';
  $fontDisplay = "'Bebas Neue', 'Arial Black', Arial, sans-serif";
  $fontSerif = "'Playfair Display', Georgia, 'Times New Roman', serif";
  $fontBody = "'Noto Sans KR', 'Apple SD Gothic Neo', 'Malgun Gothic', Arial, sans-serif";
  $accent = $accent ?? '#E5AD16';
  $bandBg = $bandBg ?? '#E5AD16';
  $bandColor = $bandColor ?? '#1A1212';
  $bandLabel = $bandLabel ?? 'MARATHON &amp; RUNNING RACE REVIEW PLATFORM';
  $badgeColor = $badgeColor ?? '#E5AD16';
  $titleEmColor = $titleEmColor ?? '#E5AD16';
  $colRuleColor = $colRuleColor ?? '#1A1212';
  $ctaBg = $ctaBg ?? '#1A1212';
  $ctaText = $ctaText ?? '#F5EFE3';
  $urlLabelColor = $urlLabelColor ?? '#C09010';
  $urlBorderColor = $urlBorderColor ?? '#E5AD16';
  $urlLinkLabel = $urlLinkLabel ?? 'DIRECT LINK';
  $expireMinutes = $expireMinutes ?? 60;
  $homeUrl = $homeUrl ?? 'https://review.pac-run.com';
  $contactUrl = $contactUrl ?? 'https://review.pac-run.com/contact';
  $headerTag = $headerTag ?? 'RACE REVIEW';
  $headerTagColor = $headerTagColor ?? '#E5AD16';
  $messageBorder = $messageBorder ?? '#E8E0D0';
@endphp
<!DOCTYPE html>
<html lang="ko" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
  <title>{{ $pageTitle }}</title>
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
    @media only screen and (max-width: 620px) {
      .email-container { width: 100% !important; }
      .px-40 { padding-left: 20px !important; padding-right: 20px !important; }
      .hero-title { font-size: 32px !important; }
      .cta-btn { padding: 16px 24px !important; font-size: 13px !important; letter-spacing: 4px !important; }
      .hide-mobile { display: none !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#EDE8DF;word-spacing:normal;">

  <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
    {{ $preheader }}&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#EDE8DF;">
    <tr>
      <td align="center" style="padding:32px 12px;">

        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#FAFAF5;border:1px solid #D8D0C0;">

          {{-- 헤더 --}}
          <tr>
            <td class="px-40" style="padding:18px 40px;background-color:#141010;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <span style="font-family:{{ $fontDisplay }};font-size:20px;letter-spacing:5px;color:#E5AD16;">PAC RUN</span>
                    <span class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:14px;color:#333333;padding:0 8px;">/</span>
                    <span class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;color:#555555;">REVIEW · SINCE 2024</span>
                  </td>
                  <td align="right" class="hide-mobile">
                    <span style="font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:3px;color:{{ $headerTagColor }};">{{ $headerTag }}</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- 상단 밴드 --}}
          <tr>
            <td class="px-40" style="padding:8px 40px;background-color:{{ $bandBg }};">
              <p style="margin:0;font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;color:{{ $bandColor }};">{!! $bandLabel !!}</p>
            </td>
          </tr>

          {{-- 히어로 --}}
          <tr>
            <td class="px-40" style="padding:40px 40px 32px;background-color:#FAFAF5;border-bottom:1px solid #E8E0D0;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td colspan="2" style="padding-bottom:20px;border-top:2px solid {{ $accent }};font-size:0;line-height:0;">&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-bottom:20px;">
                    <span style="display:inline-block;border:1px solid {{ $badgeColor }};color:{{ $badgeColor }};font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;padding:4px 12px;">{{ $badge }}</span>
                    <span class="hide-mobile" style="display:inline-block;width:20px;height:1px;background-color:#D8D0C0;vertical-align:middle;margin:0 8px;"></span>
                    <span class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:3px;color:#B8A890;">{{ date('Y.m.d') }}</span>
                  </td>
                  <td align="right" class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:80px;color:#EDE8DF;line-height:1;vertical-align:top;">
                    {{ $watermark }}
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <p class="hero-title" style="margin:0;font-family:{{ $fontSerif }};font-size:44px;font-weight:700;color:#1A1212;line-height:1.15;">
                      {!! $heroTitleHtml !!}
                    </p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding-top:20px;border-top:1px solid #E8E0D0;">
                    <p style="margin:8px 0 0;font-family:{{ $fontDisplay }};font-size:8px;letter-spacing:3px;color:#B8A890;">{{ $heroDesc }}</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- 본문 --}}
          <tr>
            <td class="px-40" style="padding:40px 40px 36px;background-color:#FFFFFF;border-bottom:1px solid #E8E0D0;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
                <tr><td width="40" height="3" style="background-color:{{ $colRuleColor }};font-size:0;line-height:0;">&nbsp;</td><td></td></tr>
              </table>

              <p style="margin:0 0 6px;font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;color:#B8A890;text-transform:uppercase;">{{ $eyebrow }}</p>

              <p style="margin:0 0 18px;font-family:{{ $fontSerif }};font-size:26px;font-weight:700;color:#1A1212;line-height:1.3;">
                안녕하세요,<br>
                <em style="font-style:italic;color:#C09010;">{{ $nick }}</em>님!
              </p>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                <tr>
                  <td width="2" style="background-color:{{ $messageBorder }};font-size:0;line-height:0;">&nbsp;</td>
                  <td style="padding-left:18px;font-family:{{ $fontBody }};font-size:14px;font-weight:300;color:#5A5040;line-height:1.85;">
                    {!! $messageHtml !!}
                  </td>
                </tr>
              </table>

              {{-- CTA --}}
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                <tr>
                  <td align="center">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td align="center" style="background-color:{{ $ctaBg }};border:2px solid {{ $ctaBg }};border-bottom:4px solid {{ $ctaBg }};">
                          <a href="{{ $url }}" target="_blank" class="cta-btn" style="display:inline-block;padding:18px 52px;font-family:{{ $fontDisplay }};font-size:15px;font-weight:bold;letter-spacing:6px;color:{{ $ctaText }};text-decoration:none;mso-padding-alt:0;">
                            <span style="mso-text-raise:15pt;">{{ $ctaLabel }}</span>
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr><td style="border-top:1px solid #F0EAE0;font-size:0;line-height:0;">&nbsp;</td></tr>
              </table>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;background-color:#FAFAF5;border:1px solid #E8E0D0;border-top:3px solid {{ $urlBorderColor }};">
                <tr>
                  <td style="padding:14px 18px;">
                    <p style="margin:0 0 8px;font-family:{{ $fontDisplay }};font-size:8px;letter-spacing:4px;color:{{ $urlLabelColor }};">{{ $urlLinkLabel }}</p>
                    <p style="margin:0;font-family:'Courier New',Courier,monospace;font-size:10px;color:#AAAAAA;line-height:1.6;word-break:break-all;">
                      <a href="{{ $url }}" target="_blank" style="color:#888888;text-decoration:underline;">{{ $url }}</a>
                    </p>
                  </td>
                </tr>
              </table>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;background-color:#FFF8F5;border:1px solid #FFE0D0;border-left:4px solid #E80043;">
                <tr>
                  <td style="padding:12px 16px;font-family:{{ $fontBody }};font-size:12px;font-weight:500;color:#CC2200;line-height:1.5;">
                    ⏱&nbsp;이 링크는 <strong>{{ $expireMinutes }}분</strong> 후 만료됩니다.
                  </td>
                </tr>
              </table>

              <p style="margin:0;font-family:{{ $fontBody }};font-size:11px;color:#C0B8A8;text-align:center;">
                본인이 요청하지 않은 경우 이 메일을 무시하세요.
              </p>
            </td>
          </tr>

          {{-- 푸터 --}}
          <tr>
            <td class="px-40" style="padding:28px 40px 24px;background-color:#1A1212;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="vertical-align:bottom;">
                    <p style="margin:0;font-family:{{ $fontDisplay }};font-size:16px;letter-spacing:5px;color:#E5AD16;">PAC RUN REVIEW</p>
                    <p style="margin:4px 0 0;font-family:{{ $fontSerif }};font-style:italic;font-size:11px;color:#4A4040;">Every race has a story.</p>
                  </td>
                  <td align="right" class="hide-mobile" style="vertical-align:bottom;">
                    <a href="{{ $homeUrl }}" style="font-family:{{ $fontBody }};font-size:11px;color:#4A4040;text-decoration:none;display:block;line-height:2;">홈페이지</a>
                    <a href="{{ $contactUrl }}" style="font-family:{{ $fontBody }};font-size:11px;color:#4A4040;text-decoration:none;display:block;line-height:2;">문의하기</a>
                  </td>
                </tr>
              </table>
              <p style="margin:16px 0 0;padding-top:14px;border-top:1px solid #2A2020;font-family:{{ $fontBody }};font-size:9px;color:#3A3030;letter-spacing:2px;text-align:center;text-transform:uppercase;">
                © {{ date('Y') }} PAC RUN. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
