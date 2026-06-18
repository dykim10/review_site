@extends('layouts.review')
@section('title', 'PAC-RUN — 마라톤 대회 리뷰')

@push('styles')
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
    }

    /* ── HERO (다크, 브랜드 스테이트먼트 — 사이트 내 유일한 다크 구간) ── */
    .hero {
        border-bottom: 1px solid var(--border);
        padding: 2.75rem 1.5rem 2.25rem;
        background:
            radial-gradient(ellipse 60% 40% at 10% 50%, rgba(255,107,53,0.07) 0%, transparent 70%),
            var(--bg);
    }
    .hero-inner { max-width: 1180px; margin: 0 auto; }

    .hero-eyebrow {
        font-size: 0.72rem; font-weight: 600; letter-spacing: 0.2em;
        color: var(--accent); text-transform: uppercase; margin-bottom: 0.5rem;
    }
    .hero-title {
        font-family: 'Bebas Neue', 'Archivo', sans-serif;
        font-size: clamp(2.6rem, 5.5vw, 4.2rem);
        letter-spacing: 0.05em; line-height: 0.92; color: var(--text);
    }
    .hero-title-em { color: var(--accent); }
    .hero-desc { margin-top: 0.9rem; font-size: 0.85rem; font-weight: 300; color: var(--text2); max-width: 440px; line-height: 1.7; }
    .hero-stats { display: flex; gap: 2.5rem; margin-top: 1.75rem; }
    .stat-num { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: 2rem; color: var(--accent); line-height: 1; }
    .stat-label { font-size: 0.7rem; color: var(--muted); margin-top: 3px; letter-spacing: 0.05em; }

    /* ── MOBILE FILTER CHIPS (라이트 크롬) ── */
    .chip-bar {
        display: none; padding: 0.85rem 1.25rem; gap: 0.5rem;
        overflow-x: auto; border-bottom: 1px solid #E8EAEE;
        background: #fff; scrollbar-width: none;
    }
    .chip-bar::-webkit-scrollbar { display: none; }
    .m-chip {
        display: inline-block; white-space: nowrap; padding: 0.38rem 0.9rem;
        border-radius: 999px; border: 1px solid #E8EAEE; font-size: 0.75rem;
        font-weight: 500; color: #5A6170; background: #F7F8FA; transition: all 0.15s;
    }
    .m-chip:hover, .m-chip-active { border-color: #E80043; color: #E80043; background: #FFF0F4; }

    /* ── PAGE LAYOUT ─────────────────────────── */
    .page-wrap {
        max-width: 1180px; margin: 0 auto; padding: 2rem 1.5rem 5rem;
        display: grid; grid-template-columns: 220px 1fr; gap: 2rem; align-items: start;
    }

    /* ── SIDEBAR (라이트 + 핑크 악센트, nav와 동일 계열) ── */
    .sidebar { position: sticky; top: 70px; }
    .s-card {
        background: #fff; border: 1px solid #E8EAEE; border-radius: 12px;
        padding: 1.25rem; box-shadow: 0 1px 3px rgba(22,24,29,0.05);
    }
    .s-card-mt { margin-top: 0.75rem; }
    .s-heading {
        font-family: 'Archivo', sans-serif; font-size: 0.68rem; font-weight: 700;
        letter-spacing: 0.15em; text-transform: uppercase; color: #9AA1AE; margin-bottom: 1.25rem;
    }
    .s-chips { display: flex; flex-direction: column; gap: 0.3rem; }
    .s-chip {
        display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        padding: 0.42rem 0.65rem; border-radius: 7px; border: 1px solid transparent;
        font-size: 0.78rem; color: #5A6170; transition: all 0.15s;
    }
    .s-chip:hover { background: #F7F8FA; color: #16181D; }
    .s-chip-active { background: #FFF0F4; border-color: rgba(232,0,67,0.25); color: #E80043; font-weight: 600; }
    .s-chip-left { display: flex; align-items: center; gap: 0.5rem; }
    .s-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .s-cnt { font-size: 0.7rem; color: #9AA1AE; font-family: 'Archivo', sans-serif; }
    .s-chip-active .s-cnt { color: #E80043; }

    /* ── MAIN CONTENT ────────────────────────── */
    .main-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .main-count { font-size: 0.78rem; color: #5A6170; }
    .main-count strong { color: #16181D; font-weight: 600; }
    .add-btn {
        display: inline-block; padding: 0.38rem 0.9rem; background: #E80043;
        border-radius: 6px; font-size: 0.76rem; font-weight: 600; color: #fff; transition: background 0.15s;
    }
    .add-btn:hover { background: #C20038; }

    /* ── SECTION HEAD ─────────────────────────── */
    .section-head { display: flex; align-items: center; gap: 0.75rem; margin: 0 0 1rem; }
    .section-head:not(:first-child) { margin-top: 2.75rem; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #16181D; font-family: 'Pretendard', sans-serif; }
    .section-count { font-size: 0.78rem; color: #9AA1AE; font-family: 'Archivo', sans-serif; }
    .section-line { flex: 1; height: 1px; background: #E8EAEE; }

    /* ── RACE CARDS (라이트) ──────────────────── */
    .race-list { display: flex; flex-direction: column; gap: 0.65rem; }
    .rc-card {
        display: block; background: #fff; border: 1px solid #E8EAEE; border-radius: 12px;
        padding: 1.2rem 1.4rem; transition: border-color 0.2s, transform 0.15s, box-shadow 0.2s;
    }
    .rc-card:hover { border-color: rgba(232,0,67,0.3); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(22,24,29,0.06); }

    .rc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.5rem; }
    .badge-row { display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }
    .badge { font-size: 0.65rem; font-weight: 700; padding: 0.18rem 0.5rem; border-radius: 4px; letter-spacing: 0.06em; }
    .b-wa-platinum { background: rgba(229,173,22,0.12); color: #B8860B; border: 1px solid rgba(229,173,22,0.3); }
    .b-wa-gold     { background: rgba(212,175,55,0.12); color: #A8821A; border: 1px solid rgba(212,175,55,0.3); }
    .b-wa-elite    { background: rgba(120,120,120,0.1); color: #6B7280; border: 1px solid rgba(120,120,120,0.25); }
    .b-wa-label    { background: rgba(205,127,50,0.12); color: #A8642A; border: 1px solid rgba(205,127,50,0.3); }
    .badge-year, .badge-city { font-size: 0.65rem; padding: 0.18rem 0.5rem; border-radius: 4px; border: 1px solid #E8EAEE; color: #5A6170; background: #F7F8FA; }

    .rc-title-row { display: flex; align-items: baseline; justify-content: space-between; gap: 0.75rem; }
    .rc-title { font-size: 1.2rem; font-weight: 700; letter-spacing: -0.01em; line-height: 1.2; color: #16181D; transition: color 0.15s; }
    .rc-card:hover .rc-title { color: #E80043; }

    .rc-rating-mini { display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0; white-space: nowrap; }
    .rc-rating-mini .star-svg { width: 13px; height: 13px; }
    .rc-rating-mini .rc-score { font-family: 'Archivo', sans-serif; font-size: 0.85rem; font-weight: 700; color: #16181D; }
    .rc-rating-mini .rc-cnt { font-size: 0.72rem; color: #9AA1AE; }

    .r-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 0.2rem 1.1rem; margin-top: 0.45rem; font-size: 0.77rem; color: #5A6170; }
    .r-meta-item { display: flex; align-items: center; gap: 0.3rem; }
    .r-meta-ico { opacity: 0.7; }

    .dist-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.7rem; }
    .dist-tag {
        font-size: 0.68rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px;
        background: rgba(232,0,67,0.07); color: #E80043; border: 1px solid rgba(232,0,67,0.18); letter-spacing: 0.04em;
    }

    .rc-footer {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;
        margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid #E8EAEE;
    }
    .rc-summary { font-size: 0.8rem; color: #5A6170; font-style: italic; }
    .rc-no-data { font-size: 0.78rem; color: #9AA1AE; }
    .rc-link { font-size: 0.78rem; font-weight: 600; color: #5A6170; transition: color 0.15s; flex-shrink: 0; }
    .rc-link:hover { color: #E80043; }
    .rc-cta { font-size: 0.78rem; font-weight: 600; color: #fff; background: #E80043; padding: 0.4rem 0.9rem; border-radius: 999px; transition: background 0.15s; flex-shrink: 0; }
    .rc-cta:hover { background: #C20038; }

    .star-svg { width: 13px; height: 13px; flex-shrink: 0; }
    .star-fill { fill: #F59E0B; }
    .star-empty-fill { fill: #E8EAEE; }

    /* ── EMPTY STATE ─────────────────────────── */
    .empty { text-align: center; padding: 3.5rem 2rem; background: #fff; border: 1px solid #E8EAEE; border-radius: 12px; }
    .empty-icon { font-size: 2.4rem; margin-bottom: 0.85rem; }
    .empty-title { font-size: 0.92rem; color: #16181D; }
    .empty-sub { font-size: 0.78rem; color: #5A6170; margin-top: 0.3rem; }

    /* ── RESPONSIVE ─────────────────────────── */
    @media (max-width: 820px) {
        .page-wrap { grid-template-columns: 1fr; padding-top: 0; gap: 0; }
        .sidebar { display: none; }
        .chip-bar { display: flex; }
        .main-content { padding-top: 1.25rem; }
    }
    @media (max-width: 480px) {
        .hero-title { font-size: 2.4rem; }
        .hero-stats { gap: 1.5rem; }
        .rc-title { font-size: 1.05rem; }
        .page-wrap { padding: 0 1rem 4rem; }
    }
</style>
@endpush

@section('content')

    @php
        // array_filter 기본 콜백은 문자열 '0'(해외 필터값)도 falsy로 간주해 제거하므로 직접 필터링
        $f = array_filter($filters, fn ($v) => $v !== null && $v !== '');
        $waLabels = ['platinum' => 'WA PLATINUM', 'gold' => 'WA GOLD', 'elite' => 'WA ELITE', 'label' => 'WA LABEL'];
    @endphp

    {{-- ────────── MOBILE CHIPS ────────── --}}
    <div class="chip-bar">
        <a href="{{ route('races.index', array_diff_key($f, ['is_domestic' => ''])) }}"
           class="m-chip {{ !isset($f['is_domestic']) ? 'm-chip-active' : '' }}">전체</a>
        <a href="{{ route('races.index', array_merge($f, ['is_domestic' => 1])) }}"
           class="m-chip {{ ($f['is_domestic'] ?? null) === '1' ? 'm-chip-active' : '' }}">국내</a>
        <a href="{{ route('races.index', array_merge($f, ['is_domestic' => 0])) }}"
           class="m-chip {{ array_key_exists('is_domestic', $f) && $f['is_domestic'] === '0' ? 'm-chip-active' : '' }}">해외</a>
        @foreach($waLabels as $val => $txt)
            <a href="{{ route('races.index', array_merge($f, ['wa_label' => $val])) }}"
               class="m-chip {{ ($f['wa_label'] ?? null) === $val ? 'm-chip-active' : '' }}">{{ $txt }}</a>
        @endforeach
        <a href="{{ route('races.index', array_merge(array_diff_key($f, ['has_review' => '']), ['has_review' => 1])) }}"
           class="m-chip {{ !empty($f['has_review']) ? 'm-chip-active' : '' }}">리뷰 있는 대회</a>
    </div>

    {{-- ────────── HERO ────────── --}}
    <section class="hero">
        <div class="hero-inner">
            <p class="hero-eyebrow">Marathon &amp; Running Race Reviews</p>
            <h1 class="hero-title">
                FIND YOUR<br><span class="hero-title-em">NEXT RACE</span>
            </h1>
            <p class="hero-desc">실제 완주자들의 솔직한 리뷰로 나에게 맞는 마라톤 대회를 찾아보세요.</p>
            <div class="hero-stats">
                <div>
                    <div class="stat-num">{{ $stats['total_races'] }}</div>
                    <div class="stat-label">전체 대회</div>
                </div>
                <div>
                    <div class="stat-num">{{ $stats['total_reviews'] }}</div>
                    <div class="stat-label">누적 리뷰</div>
                </div>
                <div>
                    <div class="stat-num">{{ $stats['races_with_reviews'] }}</div>
                    <div class="stat-label">리뷰 있는 대회</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ────────── PAGE BODY ────────── --}}
    <div class="page-wrap">

        {{-- Sidebar --}}
        <aside class="sidebar">
            {{-- 국내/해외 --}}
            <div class="s-card">
                <div class="s-heading">국내 / 해외</div>
                <div class="s-chips">
                    <a href="{{ route('races.index', array_diff_key($f, ['is_domestic' => ''])) }}"
                       class="s-chip {{ !isset($f['is_domestic']) ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>전체</span>
                    </a>
                    <a href="{{ route('races.index', array_merge($f, ['is_domestic' => 1])) }}"
                       class="s-chip {{ ($f['is_domestic'] ?? null) === '1' ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>국내</span>
                    </a>
                    <a href="{{ route('races.index', array_merge($f, ['is_domestic' => 0])) }}"
                       class="s-chip {{ array_key_exists('is_domestic', $f) && $f['is_domestic'] === '0' ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>해외</span>
                    </a>
                </div>
            </div>

            {{-- 공인 등급 --}}
            <div class="s-card s-card-mt">
                <div class="s-heading">공인 등급</div>
                <div class="s-chips">
                    <a href="{{ route('races.index', array_diff_key($f, ['wa_label' => ''])) }}"
                       class="s-chip {{ !isset($f['wa_label']) ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>전체</span>
                    </a>
                    @foreach($waLabels as $val => $txt)
                        <a href="{{ route('races.index', array_merge($f, ['wa_label' => $val])) }}"
                           class="s-chip {{ ($f['wa_label'] ?? null) === $val ? 's-chip-active' : '' }}">
                            <span class="s-chip-left"><span class="s-dot"></span>{{ $txt }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- 연도 --}}
            <div class="s-card s-card-mt">
                <div class="s-heading">연도</div>
                <div class="s-chips">
                    <a href="{{ route('races.index', array_diff_key($f, ['year' => ''])) }}"
                       class="s-chip {{ !isset($f['year']) ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>전체</span>
                    </a>
                    @foreach($years as $y)
                        <a href="{{ route('races.index', array_merge($f, ['year' => $y->year])) }}"
                           class="s-chip {{ (string)($f['year'] ?? '') === (string)$y->year ? 's-chip-active' : '' }}">
                            <span class="s-chip-left"><span class="s-dot"></span>{{ $y->year }}년</span>
                            <span class="s-cnt">{{ $y->cnt }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- 리뷰 유무 --}}
            <div class="s-card s-card-mt">
                <div class="s-heading">리뷰</div>
                <div class="s-chips">
                    <a href="{{ route('races.index', array_merge(array_diff_key($f, ['has_review' => '']), empty($f['has_review']) ? ['has_review' => 1] : [])) }}"
                       class="s-chip {{ !empty($f['has_review']) ? 's-chip-active' : '' }}">
                        <span class="s-chip-left"><span class="s-dot"></span>리뷰 있는 대회만</span>
                    </a>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <main class="main-content">
            <div class="main-bar">
                <p class="main-count">
                    다가오는 대회 <strong>{{ $upcoming->count() }}</strong> · 지난 대회 <strong>{{ $past->count() }}</strong>
                </p>
                @auth
                    @if(in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                        <a href="{{ route('admin.races.create') }}" class="add-btn">+ 대회 등록</a>
                    @endif
                @endauth
            </div>

            @php
                $parseDist = function ($distRaw) {
                    if (is_string($distRaw) && str_starts_with($distRaw, '{')) {
                        $inner = substr($distRaw, 1, -1);
                        return $inner !== '' ? str_getcsv($inner, ',', '"') : [];
                    } elseif (is_string($distRaw)) {
                        return json_decode($distRaw, true) ?? [];
                    }
                    return [];
                };
                $waLabelClassOf = fn ($v) => match ($v ?? '') {
                    'platinum' => 'b-wa-platinum', 'gold' => 'b-wa-gold',
                    'elite' => 'b-wa-elite', 'label' => 'b-wa-label', default => '',
                };
            @endphp

            {{-- ── 다가오는 대회 ── --}}
            <div class="section-head" id="upcoming">
                <span class="section-title">다가오는 대회</span>
                <span class="section-count">{{ $upcoming->count() }}개</span>
                <div class="section-line"></div>
            </div>

            <div class="race-list">
                @forelse($upcoming as $race)
                    @php
                        $distArr     = $parseDist($race->distances ?? null);
                        $reviewCount = (int) ($race->review_count ?? 0);
                        $avgRating   = (float) ($race->avg_rating ?? 0);
                    @endphp
                    <a href="{{ route('races.show', $race->id) }}" class="rc-card">
                        <div class="rc-top">
                            <div class="badge-row">
                                @if($waLabelClassOf($race->wa_label))
                                    <span class="badge {{ $waLabelClassOf($race->wa_label) }}">{{ $waLabels[$race->wa_label] }}</span>
                                @endif
                                @if($race->latest_edition_year)
                                    <span class="badge-year">{{ $race->latest_edition_year }}</span>
                                @endif
                                @if($race->city)
                                    <span class="badge-city">{{ $race->city }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="rc-title-row">
                            <span class="rc-title">{{ $race->name }}</span>
                        </div>

                        @if(count($distArr) > 0)
                            <div class="dist-tags">
                                @foreach($distArr as $d)
                                    <span class="dist-tag">{{ trim($d) }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="rc-footer">
                            @if($reviewCount > 0)
                                <span class="rc-no-data">예년 평균 ★ {{ number_format($avgRating, 1) }} ({{ $reviewCount }}건)</span>
                            @else
                                <span class="rc-no-data">신규 대회 · 아직 리뷰 없음</span>
                            @endif
                            <span class="rc-link">대회 상세보기 →</span>
                        </div>
                    </a>
                @empty
                    <div class="empty">
                        <div class="empty-icon">🏃</div>
                        <p class="empty-title">다가오는 대회가 없습니다.</p>
                        <p class="empty-sub">새 시즌 대회 일정이 등록되면 이곳에 표시됩니다.</p>
                    </div>
                @endforelse
            </div>

            {{-- ── 지난 대회 리뷰 ── --}}
            <div class="section-head">
                <span class="section-title">지난 대회 리뷰</span>
                <span class="section-count">{{ $past->count() }}개</span>
                <div class="section-line"></div>
            </div>

            <div class="race-list">
                @forelse($past as $race)
                    @php
                        $distArr     = $parseDist($race->distances ?? null);
                        $reviewCount = (int) ($race->review_count ?? 0);
                        $avgRating   = (float) ($race->avg_rating ?? 0);
                        $aiSummary   = null;
                        if (!empty($race->ai_race_summary)) {
                            $decoded   = json_decode($race->ai_race_summary, true);
                            $aiSummary = $decoded['summary'] ?? null;
                        }
                    @endphp
                    <a href="{{ route('races.show', $race->id) }}" class="rc-card">
                        <div class="rc-top">
                            <div class="badge-row">
                                @if($waLabelClassOf($race->wa_label))
                                    <span class="badge {{ $waLabelClassOf($race->wa_label) }}">{{ $waLabels[$race->wa_label] }}</span>
                                @endif
                                @if($race->latest_edition_year)
                                    <span class="badge-year">{{ $race->latest_edition_year }}</span>
                                @endif
                                @if($race->city)
                                    <span class="badge-city">{{ $race->city }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="rc-title-row">
                            <span class="rc-title">{{ $race->name }}</span>
                            @if($reviewCount > 0)
                                <span class="rc-rating-mini">
                                    <svg class="star-svg" viewBox="0 0 24 24"><polygon class="star-fill" points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>
                                    <span class="rc-score">{{ number_format($avgRating, 1) }}</span>
                                    <span class="rc-cnt">({{ $reviewCount }}건)</span>
                                </span>
                            @endif
                        </div>

                        @if(count($distArr) > 0)
                            <div class="dist-tags">
                                @foreach($distArr as $d)
                                    <span class="dist-tag">{{ trim($d) }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="rc-footer">
                            @if($aiSummary)
                                <span class="rc-summary">"{{ $aiSummary }}"</span>
                            @elseif($reviewCount > 0)
                                <span class="rc-no-data">리뷰 {{ $reviewCount }}건</span>
                            @else
                                <span class="rc-no-data">아직 등록된 리뷰가 없습니다</span>
                            @endif
                            <span class="rc-cta">후기 보기 →</span>
                        </div>
                    </a>
                @empty
                    <div class="empty">
                        <div class="empty-icon">🏁</div>
                        <p class="empty-title">조건에 맞는 지난 대회가 없습니다.</p>
                        <p class="empty-sub">다른 필터로 다시 검색해보세요.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>

@endsection
