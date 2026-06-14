<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>이메일 인증 — PAC RUN REVIEW</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:ital,wght@0,700;1,400&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background-color: #EDE8DF;
    font-family: 'Noto Sans KR', -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
    mso-line-height-rule: exactly;
  }

  .outer-wrap {
    background-color: #EDE8DF;
    padding: 48px 20px;
  }

  .email-wrapper {
    max-width: 600px;
    margin: 0 auto;
    background-color: #FAFAF5;
    border: 1px solid #D8D0C0;
  }

  /* ── 헤더 ── */
  .header {
    background-color: #141010;
    padding: 20px 40px;
  }

  .header table { width: 100%; border-collapse: collapse; }
  .header td { vertical-align: middle; }

  .header-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 5px;
    color: #E5AD16;
    line-height: 1;
  }

  .header-pipe {
    padding: 0 10px;
    color: #333;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 16px;
  }

  .header-sub {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 4px;
    color: #555;
  }

  .header-tag {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 3px;
    color: #E5AD16;
    text-align: right;
  }

  /* ── 골드 상단 배너 ── */
  .gold-band {
    background-color: #E5AD16;
    padding: 8px 40px;
  }

  .gold-band table { width: 100%; border-collapse: collapse; }
  .gold-band td { vertical-align: middle; }

  .band-text {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 5px;
    color: #1A1212;
  }

  .band-line {
    height: 1px;
    background-color: rgba(26, 18, 18, 0.2);
  }

  /* ── 히어로 (라이트) ── */
  .hero {
    background-color: #FAFAF5;
    padding: 52px 40px 44px;
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid #E8E0D0;
  }

  .hero-num {
    position: absolute;
    top: 20px;
    right: 32px;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 140px;
    color: rgba(229, 173, 22, 0.08);
    line-height: 1;
    pointer-events: none;
    user-select: none;
  }

  .hero-badge-row {
    margin-bottom: 24px;
  }

  .hero-badge {
    display: inline-block;
    background-color: transparent;
    color: #E5AD16;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 5px;
    border: 1px solid #E5AD16;
    padding: 4px 14px;
  }

  .hero-sep {
    display: inline-block;
    width: 24px;
    height: 1px;
    background-color: #D8D0C0;
    vertical-align: middle;
    margin: 0 10px;
  }

  .hero-date {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 3px;
    color: #B8A890;
    vertical-align: middle;
  }

  .hero-rule-top {
    border: none;
    border-top: 2px solid #1A1212;
    margin-bottom: 24px;
  }

  .hero-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 52px;
    font-weight: 700;
    color: #1A1212;
    line-height: 1.1;
    margin-bottom: 8px;
  }

  .hero-title em {
    font-style: italic;
    font-weight: 400;
    color: #E5AD16;
  }

  .hero-rule-bottom {
    border: none;
    border-top: 1px solid #E8E0D0;
    margin: 24px 0 16px;
  }

  .hero-desc-row {
    display: table;
    width: 100%;
  }

  .hero-desc-left {
    display: table-cell;
    vertical-align: middle;
  }

  .hero-desc {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 9px;
    letter-spacing: 3px;
    color: #B8A890;
  }

  /* ── 본문 ── */
  .body {
    background-color: #FFFFFF;
    padding: 48px 40px 44px;
    border-top: 1px solid #E8E0D0;
    border-bottom: 1px solid #E8E0D0;
  }

  .body-col-rule {
    display: block;
    height: 3px;
    background-color: #1A1212;
    width: 40px;
    margin-bottom: 24px;
  }

  .body-eyebrow {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 5px;
    color: #B8A890;
    margin-bottom: 6px;
    display: block;
    text-transform: uppercase;
  }

  .greeting {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 28px;
    font-weight: 700;
    color: #1A1212;
    margin-bottom: 20px;
    line-height: 1.3;
  }

  .greeting-name {
    font-style: italic;
    color: #C09010;
  }

  .message {
    font-size: 14px;
    font-weight: 300;
    color: #5A5040;
    line-height: 1.9;
    margin-bottom: 40px;
    border-left: 2px solid #E8E0D0;
    padding-left: 20px;
  }

  /* ── CTA ── */
  .cta-wrap {
    margin-bottom: 40px;
    text-align: center;
  }

  .cta-btn {
    display: inline-block;
    background-color: #1A1212;
    color: #F5EFE3;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 16px;
    letter-spacing: 8px;
    padding: 20px 64px;
    text-decoration: none;
    border: 2px solid #1A1212;
    border-bottom-width: 4px;
  }

  /* ── 구분선 ── */
  .divider { border: none; border-top: 1px solid #F0EAE0; margin: 36px 0; }

  /* ── URL 박스 ── */
  .url-box {
    background-color: #FAFAF5;
    border: 1px solid #E8E0D0;
    border-top: 3px solid #E5AD16;
    padding: 16px 20px;
    margin-bottom: 20px;
  }

  .url-label {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 9px;
    letter-spacing: 4px;
    color: #C09010;
    display: block;
    margin-bottom: 8px;
  }

  .url-text {
    font-size: 10px;
    color: #AAA;
    word-break: break-all;
    line-height: 1.6;
    font-family: 'Courier New', monospace;
  }

  /* ── 만료 경고 ── */
  .expire-box {
    background-color: #FFF8F5;
    border: 1px solid #FFE0D0;
    border-left: 4px solid #E80043;
    padding: 14px 18px;
    margin-bottom: 16px;
  }

  .expire-text {
    font-size: 12px;
    font-weight: 500;
    color: #CC2200;
    line-height: 1.5;
  }

  .not-me {
    font-size: 11px;
    color: #C0B8A8;
    text-align: center;
    letter-spacing: 0.3px;
  }

  /* ── 푸터 ── */
  .footer {
    background-color: #1A1212;
    padding: 32px 40px 28px;
  }

  .footer table { width: 100%; border-collapse: collapse; }
  .footer td { vertical-align: bottom; }

  .footer-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 5px;
    color: #E5AD16;
    display: block;
  }

  .footer-tagline {
    font-family: 'Playfair Display', Georgia, serif;
    font-style: italic;
    font-size: 11px;
    color: #3D3530;
    display: block;
    margin-top: 4px;
  }

  .footer-links-cell { text-align: right; }

  .footer-link {
    font-size: 11px;
    color: #4A4040;
    text-decoration: none;
    letter-spacing: 1px;
    display: block;
    line-height: 2;
  }

  .footer-copy {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #2A2020;
    font-size: 9px;
    color: #3A3030;
    letter-spacing: 2px;
    text-align: center;
    text-transform: uppercase;
  }

  /* ── 모바일 반응형 ── */
  @media screen and (max-width: 620px) {
    .outer-wrap { padding: 0 !important; }
    .email-wrapper { width: 100% !important; border-left: none !important; border-right: none !important; }

    .header { padding: 14px 20px !important; }
    .header-logo { font-size: 18px !important; }
    .header-tag { display: none !important; }

    .gold-band { padding: 7px 20px !important; }
    .band-text { font-size: 8px !important; letter-spacing: 2px !important; }

    .hero { padding: 32px 20px 26px !important; }
    .hero-title { font-size: 36px !important; }
    .hero-num { font-size: 90px !important; top: 12px !important; right: 14px !important; }
    .hero-badge { font-size: 9px !important; letter-spacing: 3px !important; }
    .hero-sep { display: none !important; }
    .hero-date { display: none !important; }
    .hero-desc { font-size: 8px !important; letter-spacing: 1px !important; }

    .body { padding: 28px 20px 24px !important; }
    .greeting { font-size: 22px !important; }
    .message { font-size: 13px !important; }

    .cta-btn {
      display: block !important;
      padding: 16px 20px !important;
      font-size: 13px !important;
      letter-spacing: 5px !important;
      text-align: center !important;
      box-sizing: border-box !important;
    }

    .url-box { padding: 12px 14px !important; }
    .expire-box { padding: 12px 14px !important; }

    .footer { padding: 22px 20px 18px !important; }
    .footer-links-cell { display: none !important; }
    .footer-logo { font-size: 14px !important; }
  }
</style>
</head>
<body>
<div class="outer-wrap">
<div class="email-wrapper">

  <!-- 헤더 -->
  <div class="header">
    <table>
      <tr>
        <td>
          <span class="header-logo">PAC RUN</span>
          <span class="header-pipe">/</span>
          <span class="header-sub">REVIEW · SINCE 2024</span>
        </td>
        <td style="text-align:right;">
          <span class="header-tag">RACE REVIEW</span>
        </td>
      </tr>
    </table>
  </div>

  <!-- 골드 밴드 -->
  <div class="gold-band">
    <span class="band-text">MARATHON &amp; RUNNING RACE REVIEW PLATFORM</span>
  </div>

  <!-- 히어로 -->
  <div class="hero">
    <div class="hero-num">01</div>

    <hr class="hero-rule-top">

    <div class="hero-badge-row">
      <span class="hero-badge">EMAIL VERIFICATION</span>
      <span class="hero-sep"></span>
      <span class="hero-date">{{ date('Y.m.d') }}</span>
    </div>

    <div class="hero-title">
      이메일 <em>인증</em><br>요청
    </div>

    <hr class="hero-rule-bottom">
    <div class="hero-desc">ACCOUNT VERIFICATION / STEP 01 OF 01</div>
  </div>

  <!-- 본문 -->
  <div class="body">

    <span class="body-col-rule"></span>
    <span class="body-eyebrow">이메일 인증 안내</span>

    <div class="greeting">
      안녕하세요,<br>
      <span class="greeting-name">{{ $user->nickname ?? $user->name ?? '러너' }}</span>님!
    </div>

    <p class="message">
      PAC RUN REVIEW에 가입해주셔서 감사합니다.<br>
      아래 버튼을 클릭하여 이메일 인증을 완료하면<br>
      리뷰 작성 및 모든 서비스를 이용하실 수 있습니다.
    </p>

    <div class="cta-wrap">
      <a href="{{ $url }}" class="cta-btn">이메일 인증하기</a>
    </div>

    <hr class="divider">

    <div class="url-box">
      <span class="url-label">DIRECT LINK</span>
      <div class="url-text">{{ $url }}</div>
    </div>

    <div class="expire-box">
      <div class="expire-text">⏱&ensp;이 링크는 <strong>60분</strong> 후 만료됩니다.</div>
    </div>

    <p class="not-me">본인이 요청하지 않은 경우 이 메일을 무시하세요.</p>

  </div>

  <!-- 푸터 -->
  <div class="footer">
    <table>
      <tr>
        <td>
          <span class="footer-logo">PAC RUN REVIEW</span>
          <span class="footer-tagline">Every race has a story.</span>
        </td>
        <td class="footer-links-cell">
          <a href="https://review.pac-run.com" class="footer-link">홈페이지</a>
          <a href="https://review.pac-run.com/contact" class="footer-link">문의하기</a>
        </td>
      </tr>
    </table>
    <div class="footer-copy">© {{ date('Y') }} PAC RUN. All rights reserved.</div>
  </div>

</div>
</div>
</body>
</html>
