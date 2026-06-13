<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $race->name }} — PAC-RUN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:       #0A0A0A;
            --surface:  #141414;
            --surface2: #1C1C1C;
            --border:   #242424;
            --accent:   #FF6B35;
            --accent-d: #E85520;
            --text:     #E8E8E8;
            --text2:    #AAAAAA;
            --muted:    #666;
            --star:     #FFB800;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--text); font-family: 'Noto Sans KR', sans-serif; font-size: 14px; line-height: 1.6; min-height: 100vh; }
        a { text-decoration: none; color: inherit; }

        /* ── HEADER ──────────────────────────────────── */
        .h-bar { position: sticky; top: 0; z-index: 200; height: 54px; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; background: rgba(10,10,10,0.92); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid var(--border); }
        .h-logo { font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; letter-spacing: 0.12em; color: var(--accent); }
        .h-logo span { color: var(--text); }
        .h-nav { display: flex; align-items: center; gap: 1.25rem; }
        .h-link { font-size: 0.78rem; font-weight: 500; color: var(--muted); letter-spacing: 0.04em; transition: color 0.15s; }
        .h-link:hover { color: var(--text); }
        .h-link-admin { color: var(--accent); }
        .h-link-admin:hover { color: var(--accent-d); }
        .h-btn { padding: 0.35rem 0.9rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; font-family: 'Noto Sans KR', sans-serif; transition: all 0.15s; }
        .h-btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text2); }
        .h-btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .h-btn-fill { background: var(--accent); color: #fff; }
        .h-btn-fill:hover { background: var(--accent-d); }
        .h-btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .h-btn-ghost:hover { color: var(--text); border-color: var(--text2); }

        /* ── RACE HERO ───────────────────────────────── */
        .race-hero {
            border-bottom: 1px solid var(--border);
            padding: 2.5rem 1.5rem 2rem;
            background:
                radial-gradient(ellipse 70% 60% at 0% 0%, rgba(255,107,53,0.07), transparent 65%),
                radial-gradient(ellipse 30% 40% at 100% 100%, rgba(255,184,0,0.03), transparent 60%),
                var(--bg);
        }
        .race-hero-inner { max-width: 1100px; margin: 0 auto; }

        .back-link { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--muted); margin-bottom: 1.75rem; transition: color 0.15s; }
        .back-link svg { transition: transform 0.15s; }
        .back-link:hover { color: var(--text2); }
        .back-link:hover svg { transform: translateX(-2px); }

        .hero-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; }
        .hero-left { flex: 1; min-width: 0; }

        .status-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.85rem; flex-wrap: wrap; }
        .badge { font-size: 0.65rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 4px; letter-spacing: 0.06em; }
        .b-receiving   { background: rgba(34,197,94,0.12);  color: #4ADE80; }
        .b-upcoming    { background: rgba(96,165,250,0.12); color: #60A5FA; }
        .b-closed      { background: rgba(239,68,68,0.1);   color: #F87171; }
        .b-ended       { background: rgba(100,100,100,0.1); color: #666; }
        .badge-city    { font-size: 0.65rem; padding: 0.2rem 0.55rem; border-radius: 4px; border: 1px solid var(--border); color: var(--muted); }
        .b-wa-platinum { background: rgba(229,173,22,0.15); color: #E5AD16; border: 1px solid rgba(229,173,22,0.3); }
        .b-wa-gold     { background: rgba(255,215,0,0.12);  color: #D4AF37; border: 1px solid rgba(212,175,55,0.3); }
        .b-wa-elite    { background: rgba(192,192,192,0.12);color: #B0B0B0; border: 1px solid rgba(192,192,192,0.3); }
        .b-wa-label    { background: rgba(205,127,50,0.12); color: #CD7F32; border: 1px solid rgba(205,127,50,0.3); }

        /* ── WA 공인 카드 ───────────────────────────────────── */
        .wa-card { border-radius: 12px; padding: 1px; }
        .wa-card-platinum { background: linear-gradient(135deg, rgba(229,173,22,0.5), rgba(229,173,22,0.15)); }
        .wa-card-gold     { background: linear-gradient(135deg, rgba(212,175,55,0.45), rgba(212,175,55,0.12)); }
        .wa-card-elite    { background: linear-gradient(135deg, rgba(192,192,192,0.4), rgba(192,192,192,0.1)); }
        .wa-card-label    { background: linear-gradient(135deg, rgba(205,127,50,0.4), rgba(205,127,50,0.1)); }
        .wa-card-inner { background: var(--surface); border-radius: 11px; padding: 1.1rem 1.2rem; }
        .wa-org { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--muted); margin-bottom: 0.35rem; }
        .wa-title { font-size: 0.78rem; font-weight: 600; color: var(--text2); margin-bottom: 0.75rem; }
        .wa-tier-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.65rem; }
        .wa-tier-badge { font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 5px; letter-spacing: 0.08em; }
        .wa-desc { font-size: 0.72rem; color: var(--muted); line-height: 1.55; }
        .wa-link { display: inline-flex; align-items: center; gap: 0.3rem; margin-top: 0.75rem; font-size: 0.72rem; color: var(--accent); transition: color 0.15s; }
        .wa-link:hover { color: var(--accent-d); }

        .race-name { font-family: 'Bebas Neue', sans-serif; font-size: clamp(2.2rem, 5.5vw, 4rem); letter-spacing: 0.04em; line-height: 0.95; color: var(--text); margin-bottom: 1.1rem; }

        .hero-meta { display: flex; flex-wrap: wrap; gap: 0.35rem 1.5rem; font-size: 0.82rem; color: var(--text2); margin-bottom: 1.25rem; }
        .hero-meta-item { display: flex; align-items: center; gap: 0.35rem; }
        .hero-meta-ico { opacity: 0.5; flex-shrink: 0; }

        .dist-strip { display: flex; flex-wrap: wrap; gap: 0.4rem; }
        .dist-pill { font-size: 0.68rem; font-weight: 700; padding: 0.22rem 0.7rem; border-radius: 999px; background: rgba(255,107,53,0.1); border: 1px solid rgba(255,107,53,0.25); color: var(--accent); letter-spacing: 0.06em; }

        .hero-right { flex-shrink: 0; }
        .hero-score-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1.1rem 1.4rem; text-align: center; min-width: 110px; }
        .score-num { font-family: 'Bebas Neue', sans-serif; font-size: 2.4rem; color: var(--star); letter-spacing: 0.04em; line-height: 1; }
        .score-stars { display: flex; gap: 2px; justify-content: center; margin: 0.35rem 0 0.25rem; }
        .score-count { font-size: 0.7rem; color: var(--muted); }

        /* ── PAGE LAYOUT ─────────────────────────────── */
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.5rem 5rem; display: grid; grid-template-columns: 1fr 270px; gap: 2.5rem; align-items: start; }

        /* ── CARDS ───────────────────────────────────── */
        .info-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.4rem 1.5rem; margin-bottom: 1.5rem; }
        .card-heading { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: var(--muted); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 2rem; }
        .detail-label { font-size: 0.7rem; color: var(--muted); margin-bottom: 0.3rem; letter-spacing: 0.04em; }
        .detail-value { font-size: 0.88rem; color: var(--text2); font-weight: 500; }
        .detail-value a { color: var(--accent); }
        .detail-value a:hover { color: var(--accent-d); }

        /* ── AI CARD ─────────────────────────────────── */
        .ai-card {
            margin-bottom: 1.5rem;
            border-radius: 12px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255,107,53,0.4), rgba(255,184,0,0.2) 50%, rgba(255,107,53,0.1));
        }
        .ai-card-inner { background: var(--surface); border-radius: 11px; padding: 1.4rem 1.5rem; }

        .ai-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
        .ai-title-row { display: flex; align-items: center; gap: 0.5rem; }
        .ai-icon { font-size: 1.05rem; }
        .ai-title { font-size: 0.72rem; font-weight: 700; color: var(--text); letter-spacing: 0.14em; text-transform: uppercase; }
        .ai-meta { font-size: 0.68rem; color: var(--muted); }

        .ai-summary-text { font-size: 0.85rem; color: var(--text2); line-height: 1.8; margin-bottom: 1.25rem; }

        .ai-points-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.1rem; }
        .ai-point-card { background: var(--bg); border-radius: 8px; padding: 0.9rem 1rem; border: 1px solid var(--border); }
        .ai-point-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.7rem; }
        .ai-point-label.pos { color: #4ADE80; }
        .ai-point-label.neg { color: #F97316; }
        .ai-point-list { list-style: none; display: flex; flex-direction: column; gap: 0.35rem; }
        .ai-point-list li { font-size: 0.78rem; color: var(--text2); display: flex; gap: 0.45rem; line-height: 1.5; }
        .ai-point-list li::before { content: '·'; color: var(--muted); flex-shrink: 0; margin-top: 0.05rem; }

        .ai-keywords { display: flex; flex-wrap: wrap; gap: 0.4rem; }
        .ai-kw { font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 4px; background: rgba(255,107,53,0.08); color: var(--accent); border: 1px solid rgba(255,107,53,0.15); }

        /* ── REVIEWS ─────────────────────────────────── */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; }
        .section-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.55rem; letter-spacing: 0.06em; color: var(--text); display: flex; align-items: baseline; gap: 0.5rem; }
        .section-title-count { font-size: 1rem; color: var(--muted); }

        .write-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.42rem 1rem; background: var(--accent); color: #fff; border-radius: 7px; font-size: 0.78rem; font-weight: 600; transition: background 0.15s; white-space: nowrap; }
        .write-btn:hover { background: var(--accent-d); }
        .write-btn-outline { background: transparent; border: 1px solid var(--accent); color: var(--accent); }
        .write-btn-outline:hover { background: rgba(255,107,53,0.08); }

        .review-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1.2rem 1.4rem; margin-bottom: 0.75rem; position: relative; overflow: hidden; transition: border-color 0.2s; }
        .review-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, var(--accent), rgba(255,107,53,0.3) 50%, transparent); opacity: 0; transition: opacity 0.25s; }
        .review-card:hover { border-color: rgba(255,107,53,0.2); }
        .review-card:hover::after { opacity: 1; }

        .review-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 0.9rem; }
        .reviewer-info { display: flex; align-items: center; gap: 0.75rem; }
        .reviewer-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: var(--accent); flex-shrink: 0; font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.06em; }
        .reviewer-meta { }
        .reviewer-name { font-size: 0.85rem; font-weight: 600; color: var(--text); line-height: 1.2; }
        .reviewer-sub { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.2rem; }
        .reviewer-date { font-size: 0.7rem; color: var(--muted); }
        .reviewer-dist { font-size: 0.65rem; font-weight: 700; padding: 0.12rem 0.45rem; border-radius: 3px; background: var(--surface2); color: var(--text2); letter-spacing: 0.04em; }

        .review-stars { display: flex; gap: 2px; }
        .star-icon { font-size: 0.85rem; }
        .star-on { color: var(--star); }
        .star-off { color: #2C2C2C; }

        .review-content { font-size: 0.85rem; color: var(--text2); line-height: 1.78; white-space: pre-line; }
        .review-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.6rem; }
        .review-tag { font-size: 0.74rem; color: var(--accent); background: rgba(255,107,53,0.08); border: 1px solid rgba(255,107,53,0.2); border-radius: 4px; padding: 0.14rem 0.45rem; font-weight: 500; }
        .review-images { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
        .review-img-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); cursor: pointer; transition: opacity 0.15s, border-color 0.15s; }
        .review-img-thumb:hover { opacity: 0.82; border-color: var(--accent); }

        .review-ai { margin-top: 1rem; padding: 0.9rem 1rem; border-radius: 7px; background: var(--bg); border: 1px solid var(--border); }
        .review-ai-head { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.45rem; }
        .review-ai-label { font-size: 0.65rem; font-weight: 700; color: var(--accent); letter-spacing: 0.12em; text-transform: uppercase; }
        .sentiment { font-size: 0.65rem; padding: 0.1rem 0.4rem; border-radius: 3px; }
        .s-pos { background: rgba(74,222,128,0.1); color: #4ADE80; }
        .s-neg { background: rgba(248,113,113,0.1); color: #F87171; }
        .s-neu { background: rgba(150,150,150,0.1); color: #999; }
        .review-ai-text { font-size: 0.79rem; color: var(--text2); line-height: 1.65; }

        .review-actions { margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid var(--border); display: flex; gap: 0.5rem; }
        .ra-btn { display: inline-flex; align-items: center; padding: 0.27rem 0.72rem; border-radius: 5px; font-size: 0.73rem; font-weight: 500; font-family: 'Noto Sans KR', sans-serif; cursor: pointer; background: transparent; transition: all 0.15s; text-decoration: none; white-space: nowrap; }
        .ra-edit { border: 1px solid var(--border); color: var(--text2); }
        .ra-edit:hover { border-color: var(--text2); color: var(--text); }
        .ra-del { border: 1px solid rgba(248,113,113,0.3); color: #F87171; }
        .ra-del:hover { border-color: #F87171; background: rgba(248,113,113,0.06); }

        /* ── REVIEWS EMPTY ───────────────────────────── */
        .reviews-empty { text-align: center; padding: 3.5rem 2rem; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; }
        .reviews-empty-icon { font-size: 2.4rem; margin-bottom: 0.75rem; }
        .reviews-empty-text { font-size: 0.88rem; color: var(--text2); }
        .reviews-empty-sub { font-size: 0.78rem; color: var(--muted); margin-top: 0.35rem; }

        /* ── PAGINATION ──────────────────────────────── */
        .pager { display: flex; justify-content: center; margin-top: 1.5rem; gap: 0.3rem; }

        /* ── SIDEBAR ─────────────────────────────────── */
        .sidebar { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 0.75rem; }
        .s-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.3rem 1.4rem; }
        .s-heading { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: var(--muted); margin-bottom: 1.1rem; }
        .s-row { display: flex; justify-content: space-between; align-items: baseline; padding: 0.5rem 0; border-bottom: 1px solid rgba(36,36,36,0.7); }
        .s-row:last-child { border-bottom: none; }
        .s-key { font-size: 0.75rem; color: var(--muted); }
        .s-val { font-size: 0.82rem; color: var(--text2); font-weight: 500; text-align: right; }
        .s-val-accent { color: var(--accent); }

        .s-divider { border: none; border-top: 1px solid var(--border); margin: 0.75rem 0; }

        .action-btn { display: block; width: 100%; padding: 0.6rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-align: center; font-family: 'Noto Sans KR', sans-serif; cursor: pointer; transition: all 0.15s; border: none; margin-bottom: 0.5rem; }
        .action-btn:last-child { margin-bottom: 0; }
        .action-primary { background: var(--accent); color: #fff; border: 1px solid var(--accent); }
        .action-primary:hover { background: var(--accent-d); border-color: var(--accent-d); }
        .action-reg { background: transparent; border: 1px solid var(--accent); color: var(--accent); }
        .action-reg:hover { background: rgba(255,107,53,0.08); }
        .action-secondary { background: transparent; border: 1px solid var(--border); color: var(--text2); }
        .action-secondary:hover { border-color: var(--text2); color: var(--text); }
        .action-disabled { background: var(--surface2); color: var(--muted); cursor: default; border: 1px solid var(--border); }

        .admin-actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .admin-link { flex: 1; padding: 0.42rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600; text-align: center; border: 1px solid var(--border); color: var(--muted); transition: all 0.15s; }
        .admin-link:hover { color: var(--text); border-color: var(--text2); }
        .admin-del { color: rgba(248,113,113,0.6); }
        .admin-del:hover { color: #F87171; border-color: #F87171; }
        .hidden { display: none !important; }

        /* ── ALERTS ──────────────────────────────────── */
        .alert { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.82rem; margin-bottom: 1rem; }
        .alert-success { background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.2); color: #4ADE80; }
        .alert-error   { background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); color: #F87171; }

        /* ── RESPONSIVE ──────────────────────────────── */
        @media (max-width: 860px) {
            .page-wrap { grid-template-columns: 1fr; gap: 1.5rem; padding-top: 1.5rem; }
            .sidebar { position: static; order: -1; flex-direction: row; flex-wrap: wrap; }
            .s-card { flex: 1; min-width: 220px; }
            .hero-right { display: none; }
        }
        @media (max-width: 580px) {
            .race-hero { padding: 1.75rem 1rem 1.5rem; }
            .page-wrap { padding: 1.25rem 1rem 3rem; }
            .ai-points-grid { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .h-nav .h-link { display: none; }
        }
    </style>
</head>
<body>

{{-- ── HEADER ── --}}
<header class="h-bar">
    <a href="{{ route('home') }}" class="h-logo">PAC<span>-RUN</span></a>
    <nav class="h-nav">
        <a href="{{ route('races.index') }}" class="h-link">대회</a>
        @auth
            <a href="{{ route('dashboard') }}" class="h-link">대시보드</a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.races.index') }}" class="h-link h-link-admin">관리자</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="h-btn h-btn-ghost">로그아웃</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="h-btn h-btn-outline">로그인</a>
            <a href="{{ route('register') }}" class="h-btn h-btn-fill">회원가입</a>
        @endauth
    </nav>
</header>

{{-- ── RACE HERO ── --}}
@php
    $distArr   = $race->distances ?? [];
    $raceDate  = \Carbon\Carbon::parse($race->race_date);
    $dayNames  = ['일','월','화','수','목','금','토'];
    $statusClass = match($race->status ?? '') {
        '접수중'   => 'b-receiving',
        '접수전'   => 'b-upcoming',
        '접수마감' => 'b-closed',
        '대회종료' => 'b-ended',
        default    => 'b-upcoming',
    };
    $waLabelClass = match($race->wa_label ?? '') {
        'platinum' => 'b-wa-platinum',
        'gold'     => 'b-wa-gold',
        'elite'    => 'b-wa-elite',
        'label'    => 'b-wa-label',
        default    => '',
    };
    $waLabelText = match($race->wa_label ?? '') {
        'platinum' => 'WA PLATINUM',
        'gold'     => 'WA GOLD',
        'elite'    => 'WA ELITE',
        'label'    => 'WA LABEL',
        default    => '',
    };
    $waCardClass = match($race->wa_label ?? '') {
        'platinum' => 'wa-card-platinum',
        'gold'     => 'wa-card-gold',
        'elite'    => 'wa-card-elite',
        'label'    => 'wa-card-label',
        default    => '',
    };
    $waDesc = match($race->wa_label ?? '') {
        'platinum' => '세계육상연맹이 인증한 최고 등급 대회입니다.',
        'gold'     => '세계육상연맹 Gold Label 공인 대회입니다.',
        'elite'    => '세계육상연맹 Elite Label 공인 대회입니다.',
        'label'    => '세계육상연맹 공인 기준을 충족한 대회입니다.',
        default    => '',
    };
@endphp

<section class="race-hero">
    <div class="race-hero-inner">
        <a href="{{ route('races.index') }}" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
            대회 목록
        </a>

        <div class="hero-top">
            <div class="hero-left">
                <div class="status-row">
                    <span class="badge {{ $statusClass }}">{{ $race->status ?? '접수전' }}</span>
                    @if($waLabelClass)
                        <span class="badge {{ $waLabelClass }}">{{ $waLabelText }}</span>
                    @endif
                    @if($race->city)
                        <span class="badge-city">{{ $race->city }}</span>
                    @endif
                </div>

                <h1 class="race-name">{{ $race->name }}</h1>

                <div class="hero-meta">
                    <span class="hero-meta-item">
                        <svg class="hero-meta-ico" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $raceDate->format('Y.m.d') }} ({{ $dayNames[$raceDate->dayOfWeek] }})
                        @if($race->race_time) &nbsp;{{ $race->race_time }} @endif
                    </span>
                    @if($race->location)
                        <span class="hero-meta-item">
                            <svg class="hero-meta-ico" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $race->location }}
                        </span>
                    @endif
                    @if($race->organizer)
                        <span class="hero-meta-item">
                            <svg class="hero-meta-ico" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            {{ $race->organizer }}
                        </span>
                    @endif
                </div>

                @if(!empty($distArr))
                    <div class="dist-strip">
                        @foreach($distArr as $d)
                            <span class="dist-pill">{{ trim($d) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($avgRating && $reviews->total() > 0)
                <div class="hero-right">
                    <div class="hero-score-card">
                        <div class="score-num">{{ number_format($avgRating, 1) }}</div>
                        <div class="score-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="font-size:0.9rem;color:{{ $i <= round($avgRating) ? 'var(--star)' : '#2C2C2C' }}">★</span>
                            @endfor
                        </div>
                        <div class="score-count">리뷰 {{ $reviews->total() }}건</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ── PAGE BODY ── --}}
<div class="page-wrap">

    {{-- ── MAIN ── --}}
    <main>

        {{-- Session Alerts --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Race Details Card --}}
        <div class="info-card">
            <div class="card-heading">대회 정보</div>
            <div class="detail-grid">
                <div>
                    <div class="detail-label">대회일</div>
                    <div class="detail-value">{{ $raceDate->format('Y년 m월 d일') }} ({{ $dayNames[$raceDate->dayOfWeek] }}){{ $race->race_time ? ' '.$race->race_time : '' }}</div>
                </div>
                <div>
                    <div class="detail-label">장소</div>
                    <div class="detail-value">{{ $race->location }}{{ $race->city ? ' · '.$race->city : '' }}</div>
                </div>
                <div>
                    <div class="detail-label">접수 기간</div>
                    <div class="detail-value">
                        {{ $race->reg_start?->format('Y.m.d') ?? '-' }} ~ {{ $race->reg_end?->format('Y.m.d') ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="detail-label">참가비</div>
                    <div class="detail-value">{{ $race->entry_fee ? number_format($race->entry_fee).'원~' : '-' }}</div>
                </div>
                @if($race->organizer)
                    <div>
                        <div class="detail-label">주최</div>
                        <div class="detail-value">{{ $race->organizer }}</div>
                    </div>
                @endif
                @if($race->website_url)
                    <div>
                        <div class="detail-label">공식 홈페이지</div>
                        <div class="detail-value"><a href="{{ $race->website_url }}" target="_blank" rel="noopener">바로가기 →</a></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 개최 이력 --}}
        @if($editions->isNotEmpty())
        <div class="info-card">
            <div class="card-heading">개최 이력</div>
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);">
                        <th style="text-align:left;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">연도</th>
                        <th style="text-align:left;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">대회일</th>
                        <th style="text-align:center;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">리뷰</th>
                        <th style="text-align:center;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">완주 기록</th>
                        <th style="text-align:left;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">상태</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($editions as $ed)
                    <tr style="border-bottom:1px solid rgba(36,36,36,0.5);">
                        <td style="padding:0.55rem 0.5rem;color:var(--text);font-weight:600;">{{ $ed->year ?: '-' }}</td>
                        <td style="padding:0.55rem 0.5rem;color:var(--text2);">{{ $ed->race_date?->format('Y.m.d') ?? '-' }}</td>
                        <td style="padding:0.55rem 0.5rem;text-align:center;color:var(--text2);">{{ $ed->reviews_count }}</td>
                        <td style="padding:0.55rem 0.5rem;text-align:center;color:var(--text2);">{{ $ed->completion_records_count }}</td>
                        <td style="padding:0.55rem 0.5rem;">
                            @php
                                $edSt = $ed->status ?? '접수전';
                                $edStCls = match($edSt) { '접수중' => 'b-receiving', '접수전' => 'b-upcoming', '접수마감' => 'b-closed', '대회종료' => 'b-ended', default => 'b-upcoming' };
                            @endphp
                            <span class="badge {{ $edStCls }}" style="font-size:0.6rem;">{{ $edSt }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- AI Summary --}}
        @if($race->ai_race_summary)
            @php $ai = $race->ai_race_summary; @endphp
            <div class="ai-card">
                <div class="ai-card-inner">
                    <div class="ai-header">
                        <div class="ai-title-row">
                            <span class="ai-icon">🤖</span>
                            <span class="ai-title">AI 참가 후기 분석</span>
                        </div>
                        <span class="ai-meta">{{ $reviews->total() }}건 리뷰 기반</span>
                    </div>

                    @if(!empty($ai['summary']))
                        <p class="ai-summary-text">{{ $ai['summary'] }}</p>
                    @endif

                    @if(!empty($ai['positives']) || !empty($ai['negatives']))
                        <div class="ai-points-grid">
                            @if(!empty($ai['positives']))
                                <div class="ai-point-card">
                                    <div class="ai-point-label pos">👍 좋았던 점</div>
                                    <ul class="ai-point-list">
                                        @foreach($ai['positives'] as $p)
                                            <li>{{ $p }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($ai['negatives']))
                                <div class="ai-point-card">
                                    <div class="ai-point-label neg">💬 아쉬웠던 점</div>
                                    <ul class="ai-point-list">
                                        @foreach($ai['negatives'] as $n)
                                            <li>{{ $n }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(!empty($ai['keywords']))
                        <div class="ai-keywords">
                            @foreach($ai['keywords'] as $kw)
                                <span class="ai-kw"># {{ $kw }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Reviews --}}
        <div>
            <div class="section-head">
                <div class="section-title">
                    참가 후기
                    <span class="section-title-count">({{ $reviews->total() }})</span>
                </div>
                @auth
                    @if(!$alreadyReviewed)
                        <a href="{{ route('reviews.create', $race) }}" class="write-btn">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            리뷰 작성
                        </a>
                    @else
                        <span style="font-size:0.76rem;color:var(--muted)">✓ 리뷰 작성 완료</span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="write-btn write-btn-outline">로그인 후 리뷰 작성</a>
                @endauth
            </div>

            @forelse($reviews as $review)
                @php
                    $initials = mb_substr($review->user->name ?? '?', 0, 1);
                @endphp
                <div class="review-card">
                    <div class="review-top">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">{{ $initials }}</div>
                            <div class="reviewer-meta">
                                <div class="reviewer-name">{{ $review->user->name }}</div>
                                <div class="reviewer-sub">
                                    <span class="reviewer-date">{{ $review->created_at->format('Y.m.d') }}</span>
                                    @if($review->distance)
                                        <span class="reviewer-dist">{{ $review->distance }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star-icon {{ $i <= $review->rating ? 'star-on' : 'star-off' }}">★</span>
                            @endfor
                        </div>
                    </div>

                    <p class="review-content">{{ $review->content }}</p>

                    @if($review->hashtags->isNotEmpty())
                        <div class="review-tags">
                            @foreach($review->hashtags as $tag)
                                <span class="review-tag">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    @php $reviewImgUrls = $review->getImageUrls(); @endphp
                    @if(!empty($reviewImgUrls))
                        <div class="review-images">
                            @foreach($reviewImgUrls as $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                    <img src="{{ $url }}" class="review-img-thumb" alt="리뷰 이미지" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($review->ai_summary)
                        <div class="review-ai">
                            <div class="review-ai-head">
                                <span class="review-ai-label">AI 요약</span>
                                @php
                                    $sentClass = match($review->sentiment ?? '') {
                                        'positive' => 's-pos',
                                        'negative' => 's-neg',
                                        default    => 's-neu',
                                    };
                                    $sentLabel = match($review->sentiment ?? '') {
                                        'positive' => '긍정',
                                        'negative' => '부정',
                                        default    => '중립',
                                    };
                                @endphp
                                <span class="sentiment {{ $sentClass }}">{{ $sentLabel }}</span>
                            </div>
                            <p class="review-ai-text">{{ $review->ai_summary }}</p>
                        </div>
                    @endif

                    @auth
                        @if($review->user_id === auth()->id())
                            <div class="review-actions">
                                <a href="{{ route('reviews.edit', $review) }}" class="ra-btn ra-edit">수정</a>
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('리뷰를 삭제하시겠습니까?')" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ra-btn ra-del">삭제</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="reviews-empty">
                    <div class="reviews-empty-icon">🏅</div>
                    <p class="reviews-empty-text">아직 등록된 리뷰가 없습니다.</p>
                    <p class="reviews-empty-sub">첫 번째 리뷰를 작성해보세요!</p>
                </div>
            @endforelse

            @if($reviews->hasPages())
                <div class="pager">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>

    </main>

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">

        {{-- WA 공인 카드 --}}
        @if($waCardClass)
            <div class="wa-card {{ $waCardClass }}">
                <div class="wa-card-inner">
                    <div class="wa-org">World Athletics</div>
                    <div class="wa-title">세계육상연맹 공인 대회</div>
                    <div class="wa-tier-row">
                        <span class="badge {{ $waLabelClass }} wa-tier-badge">{{ $waLabelText }}</span>
                    </div>
                    <p class="wa-desc">{{ $waDesc }}</p>
                    @if($race->official_url)
                        <a href="{{ $race->official_url }}" target="_blank" rel="noopener" class="wa-link">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            WA 공식 페이지
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Weather Card --}}
        @php
            $isFutureRace = $race->race_date->isFuture();
        @endphp
        <div class="s-card">
            <div class="s-heading">대회일 날씨</div>
            @if($isFutureRace)
                <p style="font-size:0.78rem;color:var(--muted);text-align:center;padding:0.75rem 0;">
                    🗓 대회 종료 후 표시됩니다
                </p>
            @elseif($weather)
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.9rem;">
                    <span style="font-size:2rem;line-height:1;">{{ \App\Services\WeatherService::conditionIcon($weather->weather_condition) }}</span>
                    <div>
                        <div style="font-size:1.3rem;font-weight:700;color:var(--text);line-height:1.1;">
                            {{ $weather->temperature !== null ? number_format($weather->temperature, 1).'°C' : '-' }}
                        </div>
                        @if($weather->weather_condition)
                        <div style="font-size:0.72rem;color:var(--muted);margin-top:0.15rem;">
                            {{ $weather->weather_condition }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="s-row">
                    <span class="s-key">습도</span>
                    <span class="s-val">{{ $weather->humidity !== null ? number_format($weather->humidity, 0).'%' : '-' }}</span>
                </div>
                <div class="s-row">
                    <span class="s-key">풍속</span>
                    <span class="s-val">{{ $weather->wind_speed !== null ? number_format($weather->wind_speed, 1).' m/s' : '-' }}</span>
                </div>
                @if($weather->precipitation !== null && $weather->precipitation > 0)
                    <div class="s-row">
                        <span class="s-key">강수량</span>
                        <span class="s-val">{{ number_format($weather->precipitation, 1) }} mm</span>
                    </div>
                @endif
                <p style="font-size:0.65rem;color:var(--muted);margin-top:0.6rem;">
                    @php
                        $rawTm = data_get($weather->raw_data, 'tm', '');
                        $obsTime = strlen($rawTm) >= 12
                            ? substr($rawTm, 8, 2) . ':' . substr($rawTm, 10, 2)
                            : ($race->weather_stn_id == 108 ? '07:00' : '08:00');
                    @endphp
                    기상청 ASOS · {{ $race->race_date->format('Y.m.d') }} {{ $obsTime }} 기준
                </p>
            @else
                <p style="font-size:0.78rem;color:var(--muted);text-align:center;padding:0.75rem 0;">
                    날씨 데이터를 불러올 수 없습니다
                </p>
            @endif
        </div>

        {{-- Registration Info --}}
        <div class="s-card">
            <div class="s-heading">접수 정보</div>
            <div class="s-row">
                <span class="s-key">접수 상태</span>
                <span class="s-val s-val-accent">{{ $race->status ?? '미정' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">접수 시작</span>
                <span class="s-val">{{ $race->reg_start?->format('Y.m.d') ?? '-' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">접수 마감</span>
                <span class="s-val">{{ $race->reg_end?->format('Y.m.d') ?? '-' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">참가비</span>
                <span class="s-val">{{ $race->entry_fee ? number_format($race->entry_fee).'원~' : '-' }}</span>
            </div>
        </div>

        {{-- 완주 기록 카드 --}}
        @if($editions->isNotEmpty())
        <div class="s-card">
            <div class="s-heading">완주 기록</div>
            @auth
                @if($myCompletion)
                    <div style="margin-bottom:0.75rem;">
                        <div style="font-size:0.75rem;color:var(--text2);font-weight:600;margin-bottom:0.2rem;">
                            {{ $myCompletion->raceEdition?->year }}년 완주
                        </div>
                        @if($myCompletion->finish_time_seconds)
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;color:var(--accent);letter-spacing:0.06em;line-height:1.1;">
                            {{ $myCompletion->finish_time_formatted }}
                        </div>
                        @endif
                        @if($myCompletion->bib_number)
                        <div style="font-size:0.72rem;color:var(--muted);margin-top:0.15rem;">번호표: {{ $myCompletion->bib_number }}</div>
                        @endif
                        @if($myCompletion->certificate_image_url)
                        <a href="{{ $myCompletion->certificate_image_url }}" target="_blank" rel="noopener"
                           style="display:inline-block;margin-top:0.5rem;font-size:0.72rem;color:var(--accent);">🏅 완주 인증서 보기</a>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('completions.destroy', $myCompletion) }}"
                          onsubmit="return confirm('완주 기록을 삭제하시겠습니까?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn action-secondary" style="font-size:0.76rem;padding:0.45rem;">기록 삭제</button>
                    </form>
                @else
                    <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.75rem;">이 대회를 완주하셨나요?</p>
                    <button type="button" class="action-btn action-reg"
                            onclick="document.getElementById('completion-form').classList.toggle('hidden')">
                        + 완주 기록 등록
                    </button>
                    <div id="completion-form" class="hidden" style="margin-top:0.85rem;padding-top:0.85rem;border-top:1px solid var(--border);">
                        <form method="POST" action="{{ route('completions.store', $race) }}" enctype="multipart/form-data">
                            @csrf
                            <select name="race_edition_id" required
                                    style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.45rem 0.65rem;font-size:0.8rem;color:var(--text);margin-bottom:0.5rem;font-family:'Noto Sans KR',sans-serif;">
                                <option value="">회차 선택 *</option>
                                @foreach($editions as $ed)
                                    <option value="{{ $ed->id }}">{{ $ed->year }}년 {{ $ed->race_date?->format('m.d') }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="finish_time" placeholder="완주 기록 (예: 3:45:30)" maxlength="10"
                                   style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.45rem 0.65rem;font-size:0.8rem;color:var(--text);margin-bottom:0.5rem;font-family:'Noto Sans KR',sans-serif;">
                            <input type="text" name="official_time" placeholder="공식 기록 (선택)" maxlength="10"
                                   style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.45rem 0.65rem;font-size:0.8rem;color:var(--text);margin-bottom:0.5rem;font-family:'Noto Sans KR',sans-serif;">
                            <input type="text" name="bib_number" placeholder="번호표 (선택)" maxlength="20"
                                   style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.45rem 0.65rem;font-size:0.8rem;color:var(--text);margin-bottom:0.5rem;font-family:'Noto Sans KR',sans-serif;">
                            <label style="display:block;font-size:0.72rem;color:var(--muted);margin-bottom:0.3rem;">완주 인증서 (선택, 10MB 이하)</label>
                            <input type="file" name="certificate" accept="image/*"
                                   style="width:100%;font-size:0.75rem;color:var(--text2);margin-bottom:0.6rem;">
                            <button type="submit" class="action-btn action-primary" style="font-size:0.78rem;padding:0.5rem;">등록하기</button>
                        </form>
                    </div>
                @endif
            @else
                <p style="font-size:0.78rem;color:var(--muted);">로그인 후 완주 기록을 등록할 수 있습니다.</p>
            @endauth
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="s-card">
            @auth
                @if(!$alreadyReviewed)
                    <a href="{{ route('reviews.create', $race) }}" class="action-btn action-primary">리뷰 작성하기</a>
                @else
                    <span class="action-btn action-disabled">✓ 리뷰 작성 완료</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="action-btn action-primary">로그인 후 리뷰 작성</a>
            @endauth

            @if($race->website_url)
                <a href="{{ $race->website_url }}" target="_blank" rel="noopener" class="action-btn action-reg">참가 등록 →</a>
            @endif

            {{-- Admin Actions --}}
            @auth
                @if(auth()->user()->role === 'super_admin')
                    <hr class="s-divider">
                    <div class="admin-actions">
                        <a href="{{ route('admin.races.edit', $race) }}" class="admin-link">수정</a>
                        <form method="POST" action="{{ route('admin.races.destroy', $race) }}" onsubmit="return confirm('정말 삭제하시겠습니까?')" style="flex:1">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link admin-del" style="width:100%;cursor:pointer;background:none;font-family:'Noto Sans KR',sans-serif">삭제</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

    </aside>

</div>

</body>
</html>
