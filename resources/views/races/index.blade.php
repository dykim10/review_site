<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAC-RUN — 마라톤 대회 리뷰</title>
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

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Noto Sans KR', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        a { text-decoration: none; color: inherit; }

        /* ── HEADER ─────────────────────────────── */
        .h-bar {
            position: sticky;
            top: 0;
            z-index: 200;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            background: rgba(10,10,10,0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }

        .h-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            letter-spacing: 0.12em;
            color: var(--accent);
        }
        .h-logo span { color: var(--text); }

        .h-nav {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .h-link {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.04em;
            transition: color 0.15s;
        }
        .h-link:hover { color: var(--text); }
        .h-link-admin { color: var(--accent); }
        .h-link-admin:hover { color: var(--accent-d); }

        .h-btn {
            padding: 0.35rem 0.9rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: 'Noto Sans KR', sans-serif;
            transition: all 0.15s;
        }
        .h-btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text2);
        }
        .h-btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .h-btn-fill {
            background: var(--accent);
            color: #fff;
        }
        .h-btn-fill:hover { background: var(--accent-d); }
        .h-btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .h-btn-ghost:hover { color: var(--text); border-color: var(--text2); }

        /* ── HERO ─────────────────────────────────── */
        .hero {
            border-bottom: 1px solid var(--border);
            padding: 2.75rem 1.5rem 2.25rem;
            background:
                radial-gradient(ellipse 60% 40% at 10% 50%, rgba(255,107,53,0.07) 0%, transparent 70%),
                var(--bg);
        }
        .hero-inner { max-width: 1180px; margin: 0 auto; }

        .hero-eyebrow {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2.6rem, 5.5vw, 4.2rem);
            letter-spacing: 0.05em;
            line-height: 0.92;
            color: var(--text);
        }
        .hero-title-em { color: var(--accent); }

        .hero-desc {
            margin-top: 0.9rem;
            font-size: 0.85rem;
            font-weight: 300;
            color: var(--text2);
            max-width: 440px;
            line-height: 1.7;
        }

        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 1.75rem;
        }
        .stat-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: var(--accent);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 3px;
            letter-spacing: 0.05em;
        }

        /* ── MOBILE FILTER CHIPS ─────────────────── */
        .chip-bar {
            display: none;
            padding: 0.85rem 1.25rem;
            gap: 0.5rem;
            overflow-x: auto;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            scrollbar-width: none;
        }
        .chip-bar::-webkit-scrollbar { display: none; }

        .m-chip {
            display: inline-block;
            white-space: nowrap;
            padding: 0.38rem 0.9rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            font-size: 0.75rem;
            color: var(--muted);
            background: var(--bg);
            transition: all 0.15s;
        }
        .m-chip:hover, .m-chip-active {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(255,107,53,0.08);
        }

        /* ── PAGE LAYOUT ─────────────────────────── */
        .page-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2rem 1.5rem 5rem;
            display: grid;
            grid-template-columns: 210px 1fr;
            gap: 2rem;
            align-items: start;
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .sidebar {
            position: sticky;
            top: 70px;
        }

        .s-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.25rem;
        }
        .s-card-mt { margin-top: 0.75rem; }

        .s-heading {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1.25rem;
        }

        .s-group { margin-bottom: 1.25rem; }
        .s-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--text2);
            margin-bottom: 0.5rem;
        }

        .s-input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.5rem 0.7rem;
            font-size: 0.8rem;
            color: var(--text);
            font-family: 'Noto Sans KR', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }
        .s-input:focus { border-color: var(--accent); }
        .s-input::placeholder { color: var(--muted); }

        .s-chips { display: flex; flex-direction: column; gap: 0.3rem; }
        .s-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.42rem 0.65rem;
            border-radius: 6px;
            border: 1px solid transparent;
            font-size: 0.78rem;
            color: var(--muted);
            transition: all 0.15s;
        }
        .s-chip:hover { background: rgba(255,255,255,0.04); color: var(--text); }
        .s-chip-active {
            background: rgba(255,107,53,0.1);
            border-color: rgba(255,107,53,0.3);
            color: var(--accent);
        }
        .s-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .s-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1rem 0;
        }

        .s-submit {
            width: 100%;
            padding: 0.55rem;
            background: var(--accent);
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            font-family: 'Noto Sans KR', sans-serif;
            transition: background 0.2s;
        }
        .s-submit:hover { background: var(--accent-d); }

        /* ── MAIN CONTENT ────────────────────────── */
        .main-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .main-count {
            font-size: 0.78rem;
            color: var(--muted);
        }
        .main-count strong { color: var(--text); font-weight: 600; }

        .add-btn {
            display: inline-block;
            padding: 0.38rem 0.9rem;
            background: var(--accent);
            border-radius: 6px;
            font-size: 0.76rem;
            font-weight: 600;
            color: #fff;
            transition: background 0.15s;
        }
        .add-btn:hover { background: var(--accent-d); }

        /* ── RACE CARDS ──────────────────────────── */
        .race-list { display: flex; flex-direction: column; gap: 0.65rem; }

        .r-card {
            display: block;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.2rem 1.4rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, transform 0.15s;
        }
        .r-card::before {
            content: '';
            position: absolute;
            inset-block: 0;
            left: 0;
            width: 3px;
            background: var(--accent);
            border-radius: 2px 0 0 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .r-card:hover { border-color: rgba(255,107,53,0.3); transform: translateX(2px); }
        .r-card:hover::before { opacity: 1; }

        .r-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .badge-row { display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }

        .badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.18rem 0.5rem;
            border-radius: 4px;
            letter-spacing: 0.06em;
        }
        .b-receiving   { background: rgba(34,197,94,0.12);  color: #4ADE80; }
        .b-upcoming    { background: rgba(96,165,250,0.12); color: #60A5FA; }
        .b-closed      { background: rgba(239,68,68,0.1);   color: #F87171; }
        .b-ended       { background: rgba(100,100,100,0.1); color: #666; }
        .b-wa-platinum { background: rgba(229,173,22,0.15); color: #E5AD16; border: 1px solid rgba(229,173,22,0.3); }
        .b-wa-gold     { background: rgba(255,215,0,0.12);  color: #D4AF37; border: 1px solid rgba(212,175,55,0.3); }
        .b-wa-elite    { background: rgba(192,192,192,0.12);color: #B0B0B0; border: 1px solid rgba(192,192,192,0.3); }
        .b-wa-label    { background: rgba(205,127,50,0.12); color: #CD7F32; border: 1px solid rgba(205,127,50,0.3); }

        .badge-city {
            font-size: 0.65rem;
            padding: 0.18rem 0.5rem;
            border-radius: 4px;
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .r-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            letter-spacing: 0.04em;
            line-height: 1.05;
            color: var(--text);
            transition: color 0.15s;
        }
        .r-card:hover .r-title { color: var(--accent); }

        .r-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.2rem 1.1rem;
            margin-top: 0.45rem;
            font-size: 0.77rem;
            color: var(--muted);
        }
        .r-meta-item { display: flex; align-items: center; gap: 0.3rem; }
        .r-meta-ico { opacity: 0.7; }

        .dist-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            margin-top: 0.7rem;
        }
        .dist-tag {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            background: rgba(255,107,53,0.09);
            color: var(--accent);
            border: 1px solid rgba(255,107,53,0.2);
            letter-spacing: 0.04em;
        }

        .r-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.9rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
        }

        /* Stars */
        .star-row { display: flex; align-items: center; gap: 0.4rem; }
        .stars-wrap { display: flex; gap: 2px; }
        .star-svg { width: 13px; height: 13px; flex-shrink: 0; }
        .star-fill { fill: var(--star); }
        .star-empty-fill { fill: #2C2C2C; }
        .r-score {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem;
            color: var(--star);
            letter-spacing: 0.06em;
        }
        .r-review-cnt { font-size: 0.72rem; color: var(--muted); }
        .r-no-review { font-size: 0.74rem; color: #444; }

        .fee-txt { font-size: 0.75rem; color: var(--muted); }
        .fee-num { color: var(--text2); font-weight: 600; }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty {
            text-align: center;
            padding: 4.5rem 2rem;
        }
        .empty-icon { font-size: 2.8rem; margin-bottom: 1rem; }
        .empty-title { font-size: 0.95rem; color: var(--text2); }
        .empty-sub { font-size: 0.8rem; color: var(--muted); margin-top: 0.35rem; }

        /* ── RESPONSIVE ─────────────────────────── */
        @media (max-width: 820px) {
            .page-wrap {
                grid-template-columns: 1fr;
                padding-top: 0;
                gap: 0;
            }
            .sidebar { display: none; }
            .chip-bar { display: flex; }
            .main-content { padding-top: 1.25rem; }
        }
        @media (max-width: 480px) {
            .h-nav .h-link { display: none; }
            .hero-title { font-size: 2.4rem; }
            .hero-stats { gap: 1.5rem; }
            .r-title { font-size: 1.25rem; }
            .page-wrap { padding: 0 1rem 4rem; }
        }
    </style>
</head>
<body>

    {{-- ────────── HEADER ────────── --}}
    <header class="h-bar">
        <a href="{{ route('home') }}" class="h-logo">PAC<span>-RUN</span></a>

        <nav class="h-nav">
            <a href="{{ route('races.index') }}" class="h-link">대회</a>
            @auth
                <a href="{{ route('dashboard') }}" class="h-link">대시보드</a>
                @if(auth()->user()->is_admin ?? false)
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

    {{-- ────────── MOBILE CHIPS ────────── --}}
    <div class="chip-bar">
        <a href="{{ route('races.index', array_diff_key(request()->query(), ['status' => ''])) }}"
           class="m-chip {{ !request('status') ? 'm-chip-active' : '' }}">전체</a>
        @foreach(['접수중','접수전','접수마감'] as $s)
            <a href="{{ route('races.index', array_merge(request()->except('status'), ['status' => $s])) }}"
               class="m-chip {{ request('status') === $s ? 'm-chip-active' : '' }}">{{ $s }}</a>
        @endforeach
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
                    <div class="stat-num">{{ $races->count() }}</div>
                    <div class="stat-label">예정 대회</div>
                </div>
                <div>
                    <div class="stat-num">{{ $races->sum('review_count') }}</div>
                    <div class="stat-label">누적 리뷰</div>
                </div>
                <div>
                    <div class="stat-num">{{ $races->filter(fn($r) => $r->review_count > 0)->count() }}</div>
                    <div class="stat-label">리뷰 있는 대회</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ────────── PAGE BODY ────────── --}}
    <div class="page-wrap">

        {{-- Sidebar --}}
        <aside class="sidebar">
            <form method="GET" action="{{ route('races.index') }}">
                {{-- 현재 status 유지 --}}
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="s-card">
                    <div class="s-heading">필터</div>

                    <div class="s-group">
                        <label class="s-label" for="city-input">도시</label>
                        <input id="city-input" type="text" name="city"
                               value="{{ request('city') }}"
                               placeholder="서울, 부산…"
                               class="s-input">
                    </div>

                    <button type="submit" class="s-submit">검색 적용</button>
                </div>
            </form>

            {{-- Status (링크 방식 — 현재 city 유지) --}}
            <div class="s-card s-card-mt">
                <div class="s-heading">접수 상태</div>
                <div class="s-chips">
                    @php
                        $cityParam = request('city') ? ['city' => request('city')] : [];
                    @endphp
                    <a href="{{ route('races.index', $cityParam) }}"
                       class="s-chip {{ !request('status') ? 's-chip-active' : '' }}">
                        <span class="s-dot"></span>전체
                    </a>
                    @foreach(['접수중','접수전','접수마감','대회종료'] as $s)
                        <a href="{{ route('races.index', array_merge($cityParam, ['status' => $s])) }}"
                           class="s-chip {{ request('status') === $s ? 's-chip-active' : '' }}">
                            <span class="s-dot"></span>{{ $s }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- WA 라벨 필터 --}}
            <div class="s-card s-card-mt">
                <div class="s-heading">공인 등급</div>
                <div class="s-chips">
                    @php
                        $baseParam = array_filter(['city' => request('city'), 'status' => request('status')]);
                    @endphp
                    <a href="{{ route('races.index', $baseParam) }}"
                       class="s-chip {{ !request('wa_label') ? 's-chip-active' : '' }}">
                        <span class="s-dot"></span>전체
                    </a>
                    @foreach([
                        'platinum' => 'WA PLATINUM',
                        'gold'     => 'WA GOLD',
                        'elite'    => 'WA ELITE',
                        'label'    => 'WA LABEL',
                    ] as $val => $txt)
                        <a href="{{ route('races.index', array_merge($baseParam, ['wa_label' => $val])) }}"
                           class="s-chip {{ request('wa_label') === $val ? 's-chip-active' : '' }}">
                            <span class="s-dot"></span>{{ $txt }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <main class="main-content">
            <div class="main-bar">
                <p class="main-count">
                    총 <strong>{{ $races->total() }}</strong>개 대회
                </p>
                @auth
                    @if(in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                        <a href="{{ route('admin.races.create') }}" class="add-btn">+ 대회 등록</a>
                    @endif
                @endauth
            </div>

            <div class="race-list">
                @forelse($races as $race)
                    @php
                        // distances 파싱 (stdClass from raw SQL)
                        $distRaw = $race->distances ?? null;
                        if (is_string($distRaw) && str_starts_with($distRaw, '{')) {
                            $inner   = substr($distRaw, 1, -1);
                            $distArr = $inner !== '' ? str_getcsv($inner, ',', '"') : [];
                        } elseif (is_string($distRaw)) {
                            $distArr = json_decode($distRaw, true) ?? [];
                        } else {
                            $distArr = [];
                        }

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

                        $avgRating   = (float)($race->avg_rating ?? 0);
                        $reviewCount = (int)($race->review_count ?? 0);
                        $raceDate    = \Carbon\Carbon::parse($race->race_date);
                        $dayNames    = ['일','월','화','수','목','금','토'];
                    @endphp

                    <a href="{{ route('races.show', $race->id) }}" class="r-card">
                        <div class="r-top">
                            <div class="badge-row">
                                <span class="badge {{ $statusClass }}">{{ $race->status ?? '접수전' }}</span>
                                @if($waLabelClass)
                                    <span class="badge {{ $waLabelClass }}">{{ $waLabelText }}</span>
                                @endif
                                @if($race->city)
                                    <span class="badge-city">{{ $race->city }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="r-title">{{ $race->name }}</div>

                        <div class="r-meta">
                            <span class="r-meta-item">
                                <svg class="r-meta-ico" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                {{ $raceDate->format('Y.m.d') }} ({{ $dayNames[$raceDate->dayOfWeek] }})
                            </span>
                            @if($race->location)
                                <span class="r-meta-item">
                                    <svg class="r-meta-ico" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ $race->location }}
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

                        <div class="r-footer">
                            {{-- 별점 --}}
                            <div class="star-row">
                                @if($reviewCount > 0)
                                    <div class="stars-wrap">
                                        @for($i = 1; $i <= 5; $i++)
                                            @php $diff = $avgRating - ($i - 1); @endphp
                                            <svg class="star-svg" viewBox="0 0 24 24">
                                                @if($diff >= 1)
                                                    <polygon class="star-fill" points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                                @elseif($diff >= 0.4)
                                                    <defs>
                                                        <linearGradient id="hg{{ $loop->index }}-{{ $i }}" x1="0" x2="1" y1="0" y2="0">
                                                            <stop offset="55%" stop-color="#FFB800"/>
                                                            <stop offset="55%" stop-color="#2C2C2C"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <polygon fill="url(#hg{{ $loop->index }}-{{ $i }})" points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                                @else
                                                    <polygon class="star-empty-fill" points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                                @endif
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="r-score">{{ number_format($avgRating, 1) }}</span>
                                    <span class="r-review-cnt">리뷰 {{ $reviewCount }}개</span>
                                @else
                                    <span class="r-no-review">아직 리뷰 없음</span>
                                @endif
                            </div>

                            {{-- 참가비 --}}
                            @if(!empty($race->entry_fee))
                                <span class="fee-txt">참가비 <span class="fee-num">{{ number_format($race->entry_fee) }}원~</span></span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="empty">
                        <div class="empty-icon">🏃</div>
                        <p class="empty-title">등록된 대회가 없습니다.</p>
                        <p class="empty-sub">다른 조건으로 다시 검색해보세요.</p>
                    </div>
                @endforelse
            </div>

            @if($races->hasPages())
                <div style="margin-top:2rem; display:flex; justify-content:center;">
                    {{ $races->withQueryString()->links() }}
                </div>
            @endif
        </main>
    </div>

</body>
</html>
