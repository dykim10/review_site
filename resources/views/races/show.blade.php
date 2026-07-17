@extends('layouts.review')
@section('title', $race->name . ' — PAC-RUN')

@push('styles')
<style>
    /* hero = 유일한 다크 구간 (index.blade.php와 동일 규칙) */
    .race-hero {
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
        --star-off: #2C2C2C;
    }

    /* 본문 = 라이트 + 핑크 액센트 (index·layouts.review와 동일) */
    .page-wrap {
        --bg:       #F7F8FA;
        --surface:  #FFFFFF;
        --surface2: #F7F8FA;
        --border:   #E8EAEE;
        --accent:   #E80043;
        --accent-d: #C20038;
        --text:     #16181D;
        --text2:    #5A6170;
        --muted:    #9AA1AE;
        --star:     #F59E0B;
        --star-off: #E8EAEE;
        background: var(--bg);
    }

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

    /* ── WA 공인 카드 ─────────────────────────── */
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

    .race-name { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: clamp(2.2rem, 5.5vw, 4rem); letter-spacing: 0.04em; line-height: 0.95; color: var(--text); margin-bottom: 1.1rem; }
    .race-name-link { color: inherit; text-decoration: none; display: block; }
    .race-name-link:hover .race-name { color: var(--accent); }

    .hero-meta { display: flex; flex-wrap: wrap; gap: 0.35rem 1.5rem; font-size: 0.82rem; color: var(--text2); margin-bottom: 1.25rem; }
    .hero-meta-item { display: flex; align-items: center; gap: 0.35rem; }
    .hero-meta-ico { opacity: 0.5; flex-shrink: 0; }

    .dist-strip { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .dist-pill { font-size: 0.68rem; font-weight: 700; padding: 0.22rem 0.7rem; border-radius: 999px; background: rgba(255,107,53,0.1); border: 1px solid rgba(255,107,53,0.25); color: var(--accent); letter-spacing: 0.06em; }

    .hero-right { flex-shrink: 0; }
    .hero-score-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1.1rem 1.4rem; text-align: center; min-width: 110px; }
    .score-num { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: 2.4rem; color: var(--star); letter-spacing: 0.04em; line-height: 1; }
    .score-stars { display: flex; gap: 2px; justify-content: center; margin: 0.35rem 0 0.25rem; }
    .score-count { font-size: 0.7rem; color: var(--muted); }

    /* ── PAGE LAYOUT ─────────────────────────────── */
    .page-wrap { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.5rem 5rem; display: grid; grid-template-columns: 1fr 270px; gap: 2.5rem; align-items: start; }

    /* ── CARDS ───────────────────────────────────── */
    .info-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
        padding: 1.4rem 1.5rem; margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(22,24,29,0.05);
    }
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
        background: linear-gradient(135deg, rgba(232,0,67,0.2), rgba(245,158,11,0.12) 50%, rgba(232,0,67,0.06));
        box-shadow: 0 1px 3px rgba(22,24,29,0.05);
    }
    .ai-card-inner { background: var(--surface); border-radius: 11px; padding: 1.4rem 1.5rem; }

    .ai-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
    .ai-title-row { display: flex; align-items: center; gap: 0.5rem; }
    .ai-icon { font-size: 1.05rem; }
    .ai-title { font-size: 0.72rem; font-weight: 700; color: var(--text); letter-spacing: 0.14em; text-transform: uppercase; }
    .ai-meta { font-size: 0.68rem; color: var(--muted); }

    .ai-summary-text { font-size: 0.85rem; color: var(--text2); line-height: 1.8; margin-bottom: 1.25rem; }

    .ai-points-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.1rem; }
    .ai-point-card { background: var(--surface2); border-radius: 8px; padding: 0.9rem 1rem; border: 1px solid var(--border); }
    .ai-point-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.7rem; }
    .ai-point-label.pos { color: #4ADE80; }
    .ai-point-label.neg { color: #F97316; }
    .ai-point-list { list-style: none; display: flex; flex-direction: column; gap: 0.35rem; }
    .ai-point-list li { font-size: 0.78rem; color: var(--text2); display: flex; gap: 0.45rem; line-height: 1.5; }
    .ai-point-list li::before { content: '·'; color: var(--muted); flex-shrink: 0; margin-top: 0.05rem; }

    .ai-keywords { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .ai-kw { font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 4px; background: rgba(232,0,67,0.07); color: var(--accent); border: 1px solid rgba(232,0,67,0.18); }

    /* ── EDITION YEAR TABS ─────────────────────── */
    .edition-tabs {
        display: flex; flex-wrap: wrap; gap: 0.4rem;
        margin-bottom: 1.25rem;
    }
    .edition-tab {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.38rem 0.9rem; border-radius: 999px;
        border: 1px solid var(--border); font-size: 0.78rem; font-weight: 500;
        color: var(--text2); background: #fff; transition: all 0.15s;
    }
    .edition-tab:hover { border-color: rgba(232,0,67,0.3); color: var(--accent); }
    .edition-tab-active {
        background: #FFF0F4; border-color: rgba(232,0,67,0.25);
        color: var(--accent); font-weight: 600;
    }
    .edition-tab-count { font-size: 0.68rem; font-family: 'Archivo', sans-serif; color: var(--muted); }
    .edition-tab-active .edition-tab-count { color: var(--accent); opacity: 0.8; }

    button.edition-tab { cursor: pointer; font-family: inherit; appearance: none; }

    #course-map-section { scroll-margin-top: 5rem; }
    #elevation-profile-section { scroll-margin-top: 5rem; }

    #reviews { scroll-margin-top: 5rem; }

    /* ── REVIEWS ─────────────────────────────────── */
    .section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; }
    .section-title {
        font-family: 'Pretendard', sans-serif; font-size: 1.1rem; font-weight: 700;
        letter-spacing: -0.01em; color: var(--text); display: flex; align-items: baseline; gap: 0.5rem;
    }
    .section-title-count { font-size: 0.78rem; font-weight: 400; color: var(--muted); font-family: 'Archivo', sans-serif; }

    .write-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.95rem; background: var(--accent); color: #fff;
        border-radius: 999px; font-size: 0.78rem; font-weight: 600;
        transition: background 0.15s; white-space: nowrap;
    }
    .write-btn:hover { background: var(--accent-d); }
    .write-btn-outline { background: transparent; border: 1px solid var(--accent); color: var(--accent); }
    .write-btn-outline:hover { background: rgba(232,0,67,0.06); }

    .review-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
        padding: 1.2rem 1.4rem; margin-bottom: 0.75rem; position: relative; overflow: hidden;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(22,24,29,0.04);
    }
    .review-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, var(--accent), rgba(232,0,67,0.25) 50%, transparent); opacity: 0; transition: opacity 0.25s; }
    .review-card:hover { border-color: rgba(232,0,67,0.25); box-shadow: 0 4px 14px rgba(22,24,29,0.06); }
    .review-card:hover::after { opacity: 1; }

    .review-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 0.9rem; }
    .reviewer-info { display: flex; align-items: center; gap: 0.75rem; }
    .reviewer-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: var(--accent); flex-shrink: 0; font-family: 'Bebas Neue', 'Archivo', sans-serif; letter-spacing: 0.06em; }
    .reviewer-meta { }
    .reviewer-name { font-size: 0.85rem; font-weight: 600; color: var(--text); line-height: 1.2; }
    .reviewer-sub { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.2rem; }
    .reviewer-date { font-size: 0.7rem; color: var(--muted); }
    .reviewer-dist { font-size: 0.65rem; font-weight: 700; padding: 0.12rem 0.45rem; border-radius: 3px; background: var(--surface2); color: var(--text2); letter-spacing: 0.04em; }

    .review-stars { display: flex; gap: 2px; }
    .star-icon { font-size: 0.85rem; }
    .star-on { color: var(--star); }
    .star-off { color: var(--star-off); }

    .review-content { font-size: 0.85rem; color: var(--text2); line-height: 1.78; white-space: pre-line; }
    .review-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.6rem; }
    .review-tag { font-size: 0.74rem; color: var(--accent); background: rgba(232,0,67,0.07); border: 1px solid rgba(232,0,67,0.18); border-radius: 4px; padding: 0.14rem 0.45rem; font-weight: 500; }
    .review-images { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
    .review-img-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); cursor: pointer; transition: opacity 0.15s, border-color 0.15s; }
    .review-img-thumb:hover { opacity: 0.82; border-color: var(--accent); }

    .review-ai { margin-top: 1rem; padding: 0.9rem 1rem; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); }
    .review-ai-head { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.45rem; }
    .review-ai-label { font-size: 0.65rem; font-weight: 700; color: var(--accent); letter-spacing: 0.12em; text-transform: uppercase; }
    .sentiment { font-size: 0.65rem; padding: 0.1rem 0.4rem; border-radius: 3px; }
    .s-pos { background: rgba(74,222,128,0.1); color: #4ADE80; }
    .s-neg { background: rgba(248,113,113,0.1); color: #F87171; }
    .s-neu { background: rgba(150,150,150,0.1); color: #999; }
    .review-ai-text { font-size: 0.79rem; color: var(--text2); line-height: 1.65; }

    .review-actions { margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid var(--border); display: flex; gap: 0.5rem; }
    .ra-btn { display: inline-flex; align-items: center; padding: 0.27rem 0.72rem; border-radius: 5px; font-size: 0.73rem; font-weight: 500; font-family: 'Pretendard', sans-serif; cursor: pointer; background: transparent; transition: all 0.15s; text-decoration: none; white-space: nowrap; }
    .ra-edit { border: 1px solid var(--border); color: var(--text2); }
    .ra-edit:hover { border-color: var(--text2); color: var(--text); }
    .ra-del { border: 1px solid rgba(248,113,113,0.3); color: #F87171; }
    .ra-del:hover { border-color: #F87171; background: rgba(248,113,113,0.06); }

    /* ── REVIEWS EMPTY ───────────────────────────── */
    .reviews-empty { text-align: center; padding: 3.5rem 2rem; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 1px 3px rgba(22,24,29,0.05); }
    .reviews-empty-icon { font-size: 2.4rem; margin-bottom: 0.75rem; }
    .reviews-empty-text { font-size: 0.88rem; color: var(--text2); }
    .reviews-empty-sub { font-size: 0.78rem; color: var(--muted); margin-top: 0.35rem; }

    /* ── PAGINATION ──────────────────────────────── */
    .pager { display: flex; justify-content: center; margin-top: 1.5rem; gap: 0.3rem; }

    /* ── SIDEBAR ─────────────────────────────────── */
    .sidebar { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 0.75rem; }
    .s-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.3rem 1.4rem; box-shadow: 0 1px 3px rgba(22,24,29,0.05); }
    .s-heading { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: var(--muted); margin-bottom: 1.1rem; }
    .s-row { display: flex; justify-content: space-between; align-items: baseline; padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
    .s-row:last-child { border-bottom: none; }
    .s-key { font-size: 0.75rem; color: var(--muted); }
    .s-val { font-size: 0.82rem; color: var(--text2); font-weight: 500; text-align: right; }
    .s-val-accent { color: var(--accent); }

    .s-divider { border: none; border-top: 1px solid var(--border); margin: 0.75rem 0; }

    .action-btn { display: block; width: 100%; padding: 0.6rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-align: center; font-family: 'Pretendard', sans-serif; cursor: pointer; transition: all 0.15s; border: none; margin-bottom: 0.5rem; }
    .action-btn:last-child { margin-bottom: 0; }
    .action-primary { background: var(--accent); color: #fff; border: 1px solid var(--accent); }
    .action-primary:hover { background: var(--accent-d); border-color: var(--accent-d); }
    .action-reg { background: transparent; border: 1px solid var(--accent); color: var(--accent); }
    .action-reg:hover { background: rgba(232,0,67,0.06); }
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
    }
</style>
@endpush

@section('content')

{{-- ── RACE HERO ── --}}
@php
    $distArr   = $race->distances ?? [];
    $raceDate  = $latestEdition?->race_date;
    $dayNames  = ['일','월','화','수','목','금','토'];
    $statusClass = match($latestEdition?->status ?? '') {
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
    $currentCalendarYear = (int) date('Y');
    $reviewsEdition = $editions->first(fn ($e) => (int) ($e->year ?? 0) === $currentCalendarYear)
        ?? $latestEdition;
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
                    @if(!empty($isUnpublishedPreview))
                        <span class="badge b-closed">미공개 대회</span>
                    @endif
                    <span class="badge {{ $statusClass }}">{{ $latestEdition?->status ?? '접수전' }}</span>
                    @if($waLabelClass)
                        <span class="badge {{ $waLabelClass }}">{{ $waLabelText }}</span>
                    @endif
                    @if($race->city)
                        <span class="badge-city">{{ $race->city }}</span>
                    @endif
                </div>

                @if($reviewsEdition)
                    <a href="{{ route('races.show-edition', [$race->id, $reviewsEdition->id]) }}#reviews" class="race-name-link">
                        <h1 class="race-name">{{ $race->name }}</h1>
                    </a>
                @else
                    <h1 class="race-name">{{ $race->name }}</h1>
                @endif

                @unless($latestEdition)
                    <p style="margin-top:0.65rem;font-size:0.82rem;color:var(--muted);">개최 정보 준비 중 — 시즌 일정이 등록되면 표시됩니다.</p>
                @endunless

                <div class="hero-meta">
                    <span class="hero-meta-item">
                        <svg class="hero-meta-ico" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $raceDate?->format('Y.m.d') ?? '-' }}{{ $raceDate ? ' ('.$dayNames[$raceDate->dayOfWeek].')' : '' }}
                        @if($latestEdition?->race_time) &nbsp;{{ $latestEdition->race_time }} @endif
                    </span>
                    @if($latestEdition?->location)
                        <span class="hero-meta-item">
                            <svg class="hero-meta-ico" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $latestEdition->location }}
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
                                <span style="font-size:0.9rem;color:{{ $i <= round($avgRating) ? 'var(--star)' : 'var(--star-off)' }}">★</span>
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
                    <div class="detail-value">{{ $raceDate?->format('Y년 m월 d일') ?? '-' }}{{ $raceDate ? ' ('.$dayNames[$raceDate->dayOfWeek].')' : '' }}{{ $latestEdition?->race_time ? ' '.$latestEdition->race_time : '' }}</div>
                </div>
                <div>
                    <div class="detail-label">장소</div>
                    <div class="detail-value">{{ $latestEdition?->location }}{{ $race->city ? ' · '.$race->city : '' }}</div>
                </div>
                <div>
                    <div class="detail-label">접수 기간</div>
                    <div class="detail-value">
                        {{ $latestEdition?->reg_start?->format('Y.m.d') ?? '-' }} ~ {{ $latestEdition?->reg_end?->format('Y.m.d') ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="detail-label">참가비</div>
                    <div class="detail-value">
                        @include('races.partials.entry-fee-display', ['edition' => $latestEdition])
                    </div>
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
                        <th style="text-align:left;padding:0.4rem 0.5rem;color:var(--muted);font-size:0.7rem;font-weight:600;letter-spacing:0.06em;">상태</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($editions as $ed)
                    @php $isActiveEd = $latestEdition && $ed->id === $latestEdition->id; @endphp
                    <tr style="border-bottom:1px solid var(--border);{{ $isActiveEd ? 'background:rgba(232,0,67,0.06);' : '' }}">
                        <td style="padding:0.55rem 0.5rem;color:var(--text);font-weight:600;">
                            <a href="{{ route('races.show-edition', [$race->id, $ed->id]) }}" style="color:inherit;text-decoration:none;{{ $isActiveEd ? 'font-weight:800;' : '' }}">
                                {{ $ed->year ?: '-' }}{{ $isActiveEd ? ' (보는 중)' : '' }}
                            </a>
                        </td>
                        <td style="padding:0.55rem 0.5rem;color:var(--text2);">{{ $ed->race_date?->format('Y.m.d') ?? '-' }}</td>
                        <td style="padding:0.55rem 0.5rem;text-align:center;color:var(--text2);">{{ $ed->reviews_count }}</td>
                        <td style="padding:0.55rem 0.5rem;">
                            @php
                                $edSt = $ed->status ?? 'upcoming';
                                $edStCls = match($edSt) {
                                    'upcoming' => 'b-upcoming',
                                    'ended' => 'b-ended',
                                    '접수중' => 'b-receiving',
                                    '접수전' => 'b-upcoming',
                                    '접수마감' => 'b-closed',
                                    '대회종료' => 'b-ended',
                                    default => 'b-upcoming',
                                };
                            @endphp
                            <span class="badge {{ $edStCls }}" style="font-size:0.6rem;">{{ $edSt }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- AI Summary (본문 없이 _meta.dirty 만 있으면 빈 카드 방지) --}}
        @php
            $ai = is_array($race->ai_race_summary) ? $race->ai_race_summary : [];
            $aiHasContent = ! empty($ai['summary'])
                || ! empty($ai['positives'])
                || ! empty($ai['negatives'])
                || ! empty($ai['keywords']);
        @endphp
        @if($aiHasContent)
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

        {{-- YouTube SNS --}}
        @if(!empty($youtubeItems) && count($youtubeItems) > 0)
        <div class="info-card" style="margin-bottom:1.5rem;">
            <div class="card-heading" style="display:flex;align-items:center;gap:0.5rem;">
                <span style="color:#FF0000;font-size:0.85rem;">▶</span> YouTube
                <span style="font-size:0.65rem;color:var(--muted);font-weight:400;margin-left:auto;">참가 후기 영상</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0.75rem;">
                @foreach($youtubeItems as $yt)
                    <a href="{{ $yt['url'] ?? '#' }}" target="_blank" rel="noopener" style="display:block;border:1px solid var(--border);border-radius:8px;overflow:hidden;transition:border-color 0.2s;" onmouseover="this.style.borderColor='rgba(232,0,67,0.3)'" onmouseout="this.style.borderColor='var(--border)'">
                        @if(!empty($yt['thumbnail_url']))
                            <div style="position:relative;aspect-ratio:16/9;overflow:hidden;background:var(--surface2);">
                                <img src="{{ $yt['thumbnail_url'] }}" alt="" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                    <div style="width:36px;height:36px;background:rgba(255,0,0,0.85);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#fff"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div style="padding:0.6rem 0.75rem;">
                            <div style="font-size:0.78rem;color:var(--text2);font-weight:500;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $yt['title'] ?? '' }}</div>
                            @if(!empty($yt['view_count']))
                                <div style="font-size:0.68rem;color:var(--muted);margin-top:0.3rem;">조회 {{ number_format($yt['view_count']) }}회</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Instagram SNS --}}
        @if(!empty($instagramItems) && count($instagramItems) > 0)
        <div class="info-card" style="margin-bottom:1.5rem;">
            <div class="card-heading" style="display:flex;align-items:center;gap:0.5rem;">
                <span style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-size:0.85rem;">◈</span>
                Instagram
                <span style="font-size:0.65rem;color:var(--muted);font-weight:400;margin-left:auto;">참가 후기 게시물</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:0.5rem;">
                @foreach($instagramItems as $ig)
                    <a href="{{ $ig['permalink'] ?? '#' }}" target="_blank" rel="noopener" style="display:block;aspect-ratio:1;overflow:hidden;border-radius:8px;border:1px solid var(--border);position:relative;" title="{{ mb_substr($ig['caption'] ?? '', 0, 100) }}">
                        @if(!empty($ig['thumbnail_url']))
                            <img src="{{ $ig['thumbnail_url'] }}" alt="" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                        @else
                            <div style="width:100%;height:100%;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.5rem;">📷</div>
                        @endif
                        @if(!empty($ig['like_count']))
                            <div style="position:absolute;bottom:4px;left:4px;background:rgba(0,0,0,0.6);border-radius:4px;padding:1px 5px;font-size:0.6rem;color:#fff;">♥ {{ number_format($ig['like_count']) }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Course map --}}
        @if($hasCourseMap)
        <div class="info-card" id="course-map-section">
            <div class="card-heading">코스 지도</div>
            @if($coursesForMap->count() > 1)
                <nav class="edition-tabs" aria-label="코스 타입" style="margin-bottom:1rem;">
                    @foreach($coursesForMap as $idx => $mapCourse)
                        <button type="button"
                                class="edition-tab course-type-tab {{ $idx === 0 ? 'edition-tab-active' : '' }}"
                                data-course-type="{{ $mapCourse->course_type }}">
                            {{ ['FULL' => '풀마라톤', 'HALF' => '하프', '10K' => '10K'][$mapCourse->course_type] ?? $mapCourse->course_type }}
                        </button>
                    @endforeach
                </nav>
            @endif
            @php $firstMapCourse = $coursesForMap->first(); @endphp
            @include('race-courses.partials.course-map', [
                'mapId'       => 'race-course-map',
                'coordinates' => $firstMapCourse->coordinates,
                'markers'     => $firstMapCourse->markers,
            ])
        </div>
        @if($coursesForMap->count() > 1)
            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var courseMapData = @json(
                    $coursesForMap->keyBy('course_type')->map(
                        fn ($c) => ['coordinates' => $c->coordinates, 'markers' => $c->markers]
                    )
                );
                document.querySelectorAll('.course-type-tab').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var type = btn.dataset.courseType;
                        document.querySelectorAll('.course-type-tab').forEach(function(b) {
                            b.classList.remove('edition-tab-active');
                        });
                        btn.classList.add('edition-tab-active');
                        var payload = courseMapData[type];
                        if (payload && typeof pacRunInitCourseMap === 'function') {
                            pacRunInitCourseMap('race-course-map', payload.coordinates, payload.markers);
                        }
                    });
                });
            });
            </script>
            @endpush
        @endif
        @endif

        {{-- Elevation profile --}}
        @if($hasElevationProfile)
        <div class="info-card" id="elevation-profile-section">
            <div class="card-heading">고저도 프로파일</div>
            @if($coursesForElevation->count() > 1)
                <nav class="edition-tabs" aria-label="고저도 코스 타입" style="margin-bottom:1rem;">
                    @foreach($coursesForElevation as $idx => $elevCourse)
                        <button type="button"
                                class="edition-tab elevation-type-tab {{ $idx === 0 ? 'edition-tab-active' : '' }}"
                                data-course-type="{{ $elevCourse->course_type }}">
                            {{ ['FULL' => '풀마라톤', 'HALF' => '하프', '10K' => '10K'][$elevCourse->course_type] ?? $elevCourse->course_type }}
                        </button>
                    @endforeach
                </nav>
            @endif
            @php $firstElevCourse = $coursesForElevation->first(); @endphp
            @include('race-courses.partials.elevation-profile', [
                'chartId' => 'race-elevation-chart',
                'profile' => $firstElevCourse->elevation_data,
            ])
        </div>
        @if($coursesForElevation->count() > 1)
            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var elevationData = @json(
                    $coursesForElevation->keyBy('course_type')->map(fn ($c) => $c->elevation_data)
                );
                document.querySelectorAll('.elevation-type-tab').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var type = btn.dataset.courseType;
                        document.querySelectorAll('.elevation-type-tab').forEach(function(b) {
                            b.classList.remove('edition-tab-active');
                        });
                        btn.classList.add('edition-tab-active');
                        var profile = elevationData[type];
                        if (profile && typeof pacRunSwitchElevationChart === 'function') {
                            pacRunSwitchElevationChart('race-elevation-chart-root', profile);
                        }
                    });
                });
            });
            </script>
            @endpush
        @endif
        @endif

        {{-- Reviews --}}
        <div id="reviews">
            @if($editions->count() > 0)
                <nav class="edition-tabs" aria-label="개최 연도">
                    @foreach($editions as $ed)
                        @php $isActiveTab = $latestEdition && $ed->id === $latestEdition->id; @endphp
                        <a href="{{ route('races.show-edition', [$race->id, $ed->id]) }}#reviews"
                           class="edition-tab {{ $isActiveTab ? 'edition-tab-active' : '' }}"
                           @if($isActiveTab) aria-current="page" @endif>
                            {{ $ed->year ?: '미정' }}
                            @if($ed->reviews_count > 0)
                                <span class="edition-tab-count">{{ $ed->reviews_count }}건</span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            @endif

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
            $isFutureRace = $latestEdition?->race_date?->isFuture() ?? false;
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
                            : ($latestEdition?->weather_stn_id == 108 ? '07:00' : '08:00');
                    @endphp
                    기상청 ASOS · {{ $latestEdition?->race_date?->format('Y.m.d') ?? '-' }} {{ $obsTime }} 기준
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
                <span class="s-val s-val-accent">{{ $latestEdition?->status ?? '미정' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">접수 시작</span>
                <span class="s-val">{{ $latestEdition?->reg_start?->format('Y.m.d') ?? '-' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">접수 마감</span>
                <span class="s-val">{{ $latestEdition?->reg_end?->format('Y.m.d') ?? '-' }}</span>
            </div>
            <div class="s-row">
                <span class="s-key">참가비</span>
                <span class="s-val">
                    @include('races.partials.entry-fee-display', ['edition' => $latestEdition])
                </span>
            </div>
        </div>

        {{-- 내 후기 / 완주 기록 (reviews SSOT) --}}
        @if($editions->isNotEmpty())
        <div class="s-card">
            <div class="s-heading">내 후기</div>
            @auth
                @if($myReview)
                    <div style="margin-bottom:0.75rem;">
                        @if($myReview->finish_time)
                        <div style="font-family:'Bebas Neue','Archivo',sans-serif;font-size:1.5rem;color:var(--accent);letter-spacing:0.06em;line-height:1.1;">
                            {{ $myReview->finish_time }}
                        </div>
                        @endif
                        @if($myReview->is_certified)
                        <div style="font-size:0.72rem;color:var(--accent);margin-top:0.35rem;">🏅 완주 라벨</div>
                        @endif
                    </div>
                    <a href="{{ route('reviews.edit', $myReview) }}" class="action-btn action-secondary" style="font-size:0.76rem;padding:0.45rem;">후기 수정</a>
                @elseif($latestEdition?->canWriteReview())
                    <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.75rem;">이 대회 후기를 작성해 보세요.</p>
                    <a href="{{ route('reviews.create', $race) }}" class="action-btn action-reg">+ 후기 작성</a>
                @else
                    <p style="font-size:0.78rem;color:var(--muted);">아직 후기 작성 기간이 아닙니다.</p>
                @endif
            @else
                <p style="font-size:0.78rem;color:var(--muted);">로그인 후 후기를 작성할 수 있습니다.</p>
            @endauth
        </div>
        @endif

        @if($latestEdition?->isUpcoming())
            @include('races.partials.feedback-tab', ['edition' => $latestEdition, 'feedbacks' => $feedbacks])
        @endif

        {{-- Action Buttons --}}
        <div class="s-card">
            @auth
                @if($latestEdition?->canWriteReview())
                    @if(!$alreadyReviewed)
                        <a href="{{ route('reviews.create', $race) }}" class="action-btn action-primary">리뷰 작성하기</a>
                    @else
                        <a href="{{ route('reviews.edit', $myReview) }}" class="action-btn action-secondary">후기 수정</a>
                    @endif
                @elseif($latestEdition?->isUpcoming())
                    <span class="action-btn action-disabled">대회 전 — 기대/개선 의견을 남겨주세요</span>
                @else
                    <span class="action-btn action-disabled">후기 작성 기간이 아닙니다</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="action-btn action-primary">로그인 후 리뷰 작성</a>
            @endauth

            {{-- 레이스 플랜 버튼 --}}
            @auth
                @if($hasOfficialGpx && $latestEdition)
                    <a href="{{ route('race-plan.create', $race) }}" class="action-btn" style="background:linear-gradient(135deg,rgba(232,0,67,0.1),rgba(245,158,11,0.06));border:1px solid rgba(232,0,67,0.28);color:var(--accent);display:flex;align-items:center;justify-content:center;gap:0.4rem;margin-top:0.5rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        AI 레이스 플랜 생성
                    </a>
                    <a href="{{ route('race-plan.index', $latestEdition) }}" class="action-btn action-secondary" style="margin-top:0.5rem;font-size:0.76rem;">내 플랜 이력</a>
                @else
                    <span class="action-btn action-disabled" style="margin-top:0.5rem;">레이스 플랜 (공식 코스 준비 중)</span>
                @endif
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
                            <button type="submit" class="admin-link admin-del" style="width:100%;cursor:pointer;background:none;font-family:'Pretendard',sans-serif">삭제</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

    </aside>

</div>

@endsection
