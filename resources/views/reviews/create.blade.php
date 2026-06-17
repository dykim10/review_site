<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>리뷰 작성 — {{ $race->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@300;400;500;600;700&family=Archivo:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:        #F7F8FA;
            --card:      #FFFFFF;
            --line:      #E8EAEE;
            --ink:       #16181D;
            --ink-2:     #5A6170;
            --ink-3:     #9AA1AE;
            --pink:      #E80043;
            --pink-soft: #FFF0F4;
            --star:      #F59E0B;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--ink); font-family: 'Pretendard', sans-serif; font-size: 15px; line-height: 1.65; min-height: 100vh; -webkit-font-smoothing: antialiased; }
        a { text-decoration: none; color: inherit; }

        /* ── HEADER ── */
        .h-bar { position: sticky; top: 0; z-index: 200; height: 56px; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); border-bottom: 1px solid var(--line); }
        .h-logo { font-family: 'Archivo', sans-serif; font-size: 1.15rem; font-weight: 700; letter-spacing: 0.06em; color: var(--pink); }
        .h-logo span { color: var(--ink); }
        .h-nav { display: flex; align-items: center; gap: 1.5rem; }
        .h-link { font-size: 0.82rem; font-weight: 500; color: var(--ink-2); transition: color 0.15s; }
        .h-link:hover { color: var(--ink); }
        .h-btn-ghost { padding: 0.3rem 0.85rem; border: 1px solid var(--line); border-radius: 6px; font-size: 0.78rem; font-weight: 500; color: var(--ink-2); background: transparent; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: all 0.15s; }
        .h-btn-ghost:hover { border-color: var(--ink-3); color: var(--ink); }

        /* ── LAYOUT ── */
        .page-wrap { max-width: 640px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }

        .back-link { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: var(--ink-3); margin-bottom: 1.5rem; transition: color 0.15s; }
        .back-link:hover { color: var(--ink-2); }
        .back-link svg { transition: transform 0.15s; }
        .back-link:hover svg { transform: translateX(-2px); }

        /* ── PAGE HEADER ── */
        .form-header { margin-bottom: 1.75rem; }
        .form-eyebrow { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: var(--pink); margin-bottom: 0.35rem; font-family: 'Archivo', sans-serif; }
        .form-title { font-size: 1.65rem; font-weight: 700; color: var(--ink); line-height: 1.2; margin-bottom: 0.3rem; }
        .form-subtitle { font-size: 0.85rem; color: var(--ink-2); }

        /* ── ERROR BOX ── */
        .error-box { background: #FFF5F5; border: 1px solid #FFC5C5; border-radius: 8px; padding: 0.9rem 1rem; margin-bottom: 1.5rem; }
        .error-box ul { list-style: none; display: flex; flex-direction: column; gap: 0.3rem; }
        .error-box li { font-size: 0.8rem; color: #C53030; display: flex; gap: 0.4rem; }
        .error-box li::before { content: '·'; flex-shrink: 0; }

        /* ── CARD ── */
        .form-card { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 1.75rem; margin-bottom: 1rem; }
        .card-title { font-size: 0.78rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 1.25rem; font-family: 'Archivo', sans-serif; }

        /* ── FIELD ── */
        .field { margin-bottom: 1.4rem; }
        .field:last-child { margin-bottom: 0; }
        .field-label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--ink-2); margin-bottom: 0.45rem; }
        .field-hint { font-size: 0.72rem; color: var(--ink-3); font-weight: 400; margin-left: 0.35rem; }
        .field-input { width: 100%; background: var(--bg); border: 1px solid var(--line); border-radius: 8px; padding: 0.65rem 0.9rem; font-size: 0.9rem; color: var(--ink); font-family: 'Pretendard', sans-serif; outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
        .field-input:focus { border-color: var(--pink); }
        .field-input::placeholder { color: var(--ink-3); }
        .field-error { font-size: 0.75rem; color: var(--pink); margin-top: 0.35rem; }
        .form-divider { border: none; border-top: 1px solid var(--line); margin: 1.5rem 0; }

        /* ── SEGMENT BUTTONS (코스 타입) ── */
        .seg-group { display: flex; gap: 0.5rem; }
        .seg-item { flex: 1; }
        .seg-item input { position: absolute; opacity: 0; width: 0; height: 0; }
        .seg-btn { display: block; text-align: center; padding: 0.6rem 0.5rem; border: 1.5px solid var(--line); border-radius: 8px; font-size: 0.82rem; font-weight: 600; color: var(--ink-2); cursor: pointer; transition: all 0.15s; background: var(--bg); }
        .seg-item input:checked + .seg-btn { border-color: var(--pink); color: var(--pink); background: var(--pink-soft); }
        .seg-btn:hover { border-color: var(--ink-3); color: var(--ink); }
        .seg-item input:checked + .seg-btn:hover { border-color: var(--pink); color: var(--pink); }
        .seg-sub { font-size: 0.65rem; font-weight: 400; color: inherit; display: block; margin-top: 0.1rem; opacity: 0.7; font-family: 'Archivo', sans-serif; }

        /* ── FINISH TIME ── */
        .time-row { display: flex; align-items: center; gap: 0.5rem; }
        .time-input { width: 64px; text-align: center; font-family: 'Archivo', sans-serif; font-size: 1.1rem; font-weight: 600; color: var(--ink); background: var(--bg); border: 1.5px solid var(--line); border-radius: 8px; padding: 0.55rem 0.5rem; outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
        .time-input:focus { border-color: var(--pink); }
        .time-sep { font-size: 1.2rem; font-weight: 700; color: var(--ink-3); font-family: 'Archivo', sans-serif; line-height: 1; }
        .time-label { font-size: 0.7rem; color: var(--ink-3); text-align: center; margin-top: 0.2rem; }

        /* ── SOURCE TABS ── */
        .source-tabs { display: flex; border: 1px solid var(--line); border-radius: 10px; overflow: hidden; margin-bottom: 1rem; }
        .source-tab { flex: 1; padding: 0.65rem 0.5rem; text-align: center; font-size: 0.8rem; font-weight: 600; color: var(--ink-3); cursor: pointer; border-right: 1px solid var(--line); transition: all 0.15s; background: var(--bg); user-select: none; }
        .source-tab:last-child { border-right: none; }
        .source-tab.active { background: var(--card); color: var(--pink); }
        .source-tab:hover:not(.active) { color: var(--ink-2); }
        .source-panel { display: none; }
        .source-panel.active { display: block; }

        /* Watch parse zone */
        .watch-zone { border: 2px dashed var(--line); border-radius: 10px; padding: 1.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
        .watch-zone:hover { border-color: var(--ink-3); background: #FAFBFC; }
        .watch-zone-ico { font-size: 2rem; margin-bottom: 0.4rem; }
        .watch-zone-txt { font-size: 0.82rem; color: var(--ink-2); }
        .watch-zone-txt small { display: block; font-size: 0.72rem; color: var(--ink-3); margin-top: 0.2rem; }
        .parse-result { background: var(--pink-soft); border: 1px solid #FFC5D5; border-radius: 8px; padding: 0.9rem 1rem; margin-top: 0.75rem; font-size: 0.82rem; color: var(--ink); display: none; }
        .parse-result.show { display: block; }
        .parse-row { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.3rem; }
        .parse-item { font-family: 'Archivo', sans-serif; }
        .parse-item span { font-size: 0.7rem; color: var(--ink-3); display: block; }
        .parse-item strong { font-size: 0.95rem; font-weight: 600; color: var(--pink); }
        .parse-loading { font-size: 0.82rem; color: var(--ink-3); margin-top: 0.6rem; display: none; text-align: center; }
        .parse-error { font-size: 0.8rem; color: var(--pink); margin-top: 0.6rem; display: none; }

        .strava-coming { padding: 1.25rem; border: 1px dashed var(--line); border-radius: 10px; text-align: center; }
        .strava-coming p { font-size: 0.82rem; color: var(--ink-3); margin-top: 0.4rem; }

        /* ── STAR RATING ── */
        .star-rating { display: flex; gap: 0.25rem; }
        .star-label { cursor: pointer; display: flex; }
        .star-label input { position: absolute; opacity: 0; width: 0; height: 0; }
        .star-glyph { font-size: 2.2rem; line-height: 1; color: var(--line); transition: color 0.12s, transform 0.1s; }
        .star-glyph:hover { transform: scale(1.1); }

        /* ── TEXTAREA ── */
        .field-textarea { resize: vertical; min-height: 160px; }
        .char-counter { font-size: 0.72rem; color: var(--ink-3); text-align: right; margin-top: 0.3rem; font-family: 'Archivo', sans-serif; }
        .char-counter.warn { color: #D97706; }
        .char-counter.limit { color: var(--pink); }

        /* ── TAG INPUT ── */
        .tag-wrap { border: 1px solid var(--line); border-radius: 8px; background: var(--bg); padding: 0.4rem 0.65rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; cursor: text; min-height: 42px; transition: border-color 0.2s; }
        .tag-wrap:focus-within { border-color: var(--pink); }
        .tag-chip { display: inline-flex; align-items: center; gap: 0.25rem; background: var(--pink-soft); border: 1px solid #FFC5D5; color: var(--pink); border-radius: 4px; padding: 0.18rem 0.5rem; font-size: 0.78rem; font-weight: 500; }
        .tag-chip-rm { background: none; border: none; color: var(--pink); cursor: pointer; padding: 0 0 0 0.1rem; font-size: 0.8rem; line-height: 1; opacity: 0.65; }
        .tag-chip-rm:hover { opacity: 1; }
        .tag-text { border: none; background: transparent; color: var(--ink); font-size: 0.85rem; outline: none; font-family: 'Pretendard', sans-serif; min-width: 130px; flex: 1; padding: 0.1rem 0; }
        .tag-text::placeholder { color: var(--ink-3); font-size: 0.8rem; }
        .tag-hint { font-size: 0.72rem; color: var(--ink-3); margin-top: 0.25rem; }

        /* ── IMAGE UPLOAD ── */
        .img-zone { border: 2px dashed var(--line); border-radius: 10px; padding: 1.2rem; text-align: center; cursor: pointer; transition: all 0.2s; }
        .img-zone:hover, .img-zone.drag { border-color: var(--pink); background: var(--pink-soft); }
        .img-zone.full { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
        .img-zone-ico { font-size: 1.6rem; margin-bottom: 0.3rem; }
        .img-zone-txt { font-size: 0.8rem; color: var(--ink-2); }
        .img-zone-txt strong { color: var(--pink); }
        .img-zone-txt small { display: block; font-size: 0.7rem; margin-top: 0.15rem; color: var(--ink-3); }
        .img-toolbar { display: flex; align-items: center; justify-content: space-between; margin-top: 0.6rem; }
        .img-add-btn { padding: 0.3rem 0.75rem; background: transparent; border: 1px solid var(--line); color: var(--ink-2); border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.15s; font-family: 'Pretendard', sans-serif; }
        .img-add-btn:hover:not(:disabled) { border-color: var(--pink); color: var(--pink); }
        .img-add-btn:disabled { opacity: 0.38; cursor: not-allowed; }
        .img-counter { font-size: 0.72rem; color: var(--ink-3); font-family: 'Archivo', sans-serif; }
        .img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(76px, 1fr)); gap: 0.45rem; margin-top: 0.6rem; }
        .pi-item { position: relative; }
        .pi-img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 6px; border: 1px solid var(--line); display: block; }
        .pi-rm { position: absolute; top: 3px; right: 3px; width: 20px; height: 20px; background: rgba(0,0,0,0.55); color: #fff; border: none; border-radius: 50%; font-size: 0.7rem; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
        .pi-rm:hover { background: var(--pink); }

        /* ── SUBMIT ── */
        .btn-row { display: flex; gap: 0.75rem; align-items: center; margin-top: 1.5rem; }
        .btn-submit { padding: 0.7rem 1.8rem; background: var(--pink); color: #fff; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: opacity 0.15s; }
        .btn-submit:hover { opacity: 0.88; }
        .btn-cancel { padding: 0.7rem 1.2rem; background: transparent; color: var(--ink-2); border: 1px solid var(--line); border-radius: 8px; font-size: 0.88rem; font-weight: 500; transition: all 0.15s; }
        .btn-cancel:hover { color: var(--ink); border-color: var(--ink-3); }

        select.field-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA1AE' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.2rem; cursor: pointer;
        }

        @media (max-width: 580px) {
            .page-wrap { padding: 1.25rem 1rem 3rem; }
            .form-card { padding: 1.25rem; }
            .h-nav .h-link { display: none; }
            .seg-group { gap: 0.35rem; }
        }
    </style>
</head>
<body>

<header class="h-bar">
    <a href="{{ route('home') }}" class="h-logo">PAC<span>-RUN</span></a>
    <nav class="h-nav">
        <a href="{{ route('races.index') }}" class="h-link">대회</a>
        @auth
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="h-btn-ghost">로그아웃</button>
            </form>
        @endauth
    </nav>
</header>

<div class="page-wrap">
    <a href="{{ route('races.show', $race) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ $race->name }}
    </a>

    <div class="form-header">
        <p class="form-eyebrow">Review</p>
        <h1 class="form-title">참가 후기 작성</h1>
        <p class="form-subtitle">{{ $race->name }}</p>
    </div>

    @if($errors->any())
        <div class="error-box">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.store', $race) }}" enctype="multipart/form-data" id="review-form">
        @csrf

        {{-- ① 참가 정보 --}}
        <div class="form-card">
            <p class="card-title">① 참가 정보</p>

            {{-- 참가 연도 --}}
            @if($editions->isNotEmpty())
            <div class="field">
                <label class="field-label">참가 연도 <span class="field-hint">(선택)</span></label>
                <select name="race_edition_id" class="field-input">
                    <option value="">연도 선택 안 함</option>
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" @selected(old('race_edition_id') == $ed->id)>
                            {{ $ed->year }}년@if($ed->race_date) · {{ \Carbon\Carbon::parse($ed->race_date)->format('m월 d일') }}@endif
                        </option>
                    @endforeach
                </select>
                @error('race_edition_id')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- 코스 타입 --}}
            <div class="field">
                <label class="field-label">코스 타입</label>
                <div class="seg-group">
                    @foreach(['FULL' => ['풀마라톤', '42.195km'], 'HALF' => ['하프마라톤', '21km'], '10K' => ['10K', '10km']] as $val => [$label, $sub])
                        <label class="seg-item">
                            <input type="radio" name="course_type" value="{{ $val }}"
                                   @checked(old('course_type') === $val)>
                            <span class="seg-btn">{{ $label }}<span class="seg-sub">{{ $sub }}</span></span>
                        </label>
                    @endforeach
                </div>
                @error('course_type')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- 참가 거리 (기존 호환) --}}
            <div class="field">
                <label class="field-label">참가 거리 <span class="text-pink-500">*</span></label>
                <select name="distance" class="field-input">
                    <option value="">선택해주세요</option>
                    @php
                        $raceDistances = array_map('trim', $race->distances ?? []);
                        $options = !empty($raceDistances) ? $raceDistances : ['5K','10K','하프','풀','기타'];
                    @endphp
                    @foreach($options as $d)
                        <option value="{{ $d }}" @selected(old('distance') === $d)>{{ $d }}</option>
                    @endforeach
                </select>
                @error('distance')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- 완주기록 --}}
            <div class="field">
                <label class="field-label">완주기록 <span class="field-hint">(선택 — H:MM:SS)</span></label>
                @php
                    $ftParts = explode(':', old('finish_time', '::'));
                    $ftH = $ftParts[0] ?? '';
                    $ftM = $ftParts[1] ?? '';
                    $ftS = $ftParts[2] ?? '';
                @endphp
                <input type="hidden" name="finish_time" id="finish-time-hidden" value="{{ old('finish_time') }}">
                <div class="time-row">
                    <div>
                        <input type="number" id="ft-h" class="time-input" placeholder="0" min="0" max="99" value="{{ $ftH }}">
                        <div class="time-label">시간</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" id="ft-m" class="time-input" placeholder="00" min="0" max="59" value="{{ $ftM }}">
                        <div class="time-label">분</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" id="ft-s" class="time-input" placeholder="00" min="0" max="59" value="{{ $ftS }}">
                        <div class="time-label">초</div>
                    </div>
                </div>
                @error('finish_time')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ② 기록 데이터 연결 --}}
        <div class="form-card">
            <p class="card-title">② 기록 데이터 연결 <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--ink-3);">(선택)</span></p>

            <input type="hidden" name="source" id="source-hidden" value="{{ old('source', 'manual') }}">
            <input type="hidden" name="parsed_watch_data" id="parsed-watch-hidden" value="{{ old('parsed_watch_data') }}">

            <div class="source-tabs">
                <div class="source-tab" data-panel="strava" data-source="strava">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:inline;vertical-align:-2px;margin-right:3px"><path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/></svg>
                    Strava
                </div>
                <div class="source-tab active" data-panel="watch" data-source="image">
                    📷 워치 스크린샷
                </div>
                <div class="source-tab" data-panel="manual" data-source="manual">
                    ✏️ 직접 입력
                </div>
            </div>

            {{-- Strava 패널 (Phase 4 예정) --}}
            <div class="source-panel" id="panel-strava">
                <div class="strava-coming">
                    <div style="font-size:1.8rem;">🚧</div>
                    <p>Strava 연동은 추후 지원 예정입니다.</p>
                    <p style="margin-top:0.25rem;font-size:0.75rem;">워치 스크린샷 또는 직접 입력을 사용해주세요.</p>
                </div>
            </div>

            {{-- 워치 스크린샷 패널 --}}
            <div class="source-panel active" id="panel-watch">
                <input type="file" id="watch-img-input" accept="image/*" style="display:none">
                <div class="watch-zone" id="watch-zone">
                    <div class="watch-zone-ico">⌚</div>
                    <div class="watch-zone-txt">
                        워치 스크린샷을 <strong>클릭</strong>하거나 끌어다 놓으세요
                        <small>운동 완료 화면 캡처 → AI가 완주기록·페이스·심박수를 자동 추출합니다</small>
                    </div>
                </div>
                <div class="parse-loading" id="parse-loading">⏳ AI가 스크린샷을 분석 중입니다...</div>
                <div class="parse-error" id="parse-error"></div>
                <div class="parse-result" id="parse-result">
                    <strong>✓ 파싱 완료 — 완주기록이 자동 입력됐습니다</strong>
                    <div class="parse-row" id="parse-detail"></div>
                </div>
            </div>

            {{-- 직접 입력 패널 --}}
            <div class="source-panel" id="panel-manual">
                <p style="font-size:0.82rem;color:var(--ink-2);padding:0.75rem 0;">
                    위 ① 참가 정보의 완주기록을 직접 입력하면 됩니다.
                </p>
            </div>
        </div>

        {{-- ③ 평점 + 본문 --}}
        <div class="form-card">
            <p class="card-title">③ 후기 작성</p>

            <div class="field" x-data="{ rating: {{ old('rating', 0) }} }">
                <label class="field-label">평점 <span style="color:var(--pink)">*</span></label>
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="star-label">
                            <input type="radio" name="rating" value="{{ $i }}"
                                   x-on:change="rating = {{ $i }}"
                                   @checked(old('rating') == $i)>
                            <span class="star-glyph" :style="rating >= {{ $i }} ? 'color:var(--star)' : ''">★</span>
                        </label>
                    @endfor
                </div>
                @error('rating')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <hr class="form-divider">

            <div class="field" x-data="{ len: {{ strlen(old('content', '')) }} }">
                <label class="field-label">
                    리뷰 내용 <span style="color:var(--pink)">*</span>
                    <span class="field-hint">(최소 10자, 최대 2000자)</span>
                </label>
                <textarea name="content" rows="8"
                          class="field-input field-textarea"
                          placeholder="코스, 분위기, 운영, 기념품 등 솔직한 후기를 자유롭게 작성해주세요."
                          @input="len = $event.target.value.length"
                          maxlength="2000">{{ old('content') }}</textarea>
                <p class="char-counter" :class="len >= 1900 ? 'limit' : len >= 1500 ? 'warn' : ''"
                   x-text="`${len} / 2000`"></p>
                @error('content')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ④ 해시태그 + 사진 --}}
        <div class="form-card">
            <p class="card-title">④ 태그 &amp; 사진</p>

            <div class="field">
                <label class="field-label">해시태그 <span class="field-hint">(선택, 최대 10개)</span></label>
                <input type="hidden" name="hashtags" id="hashtag-hidden" value="{{ old('hashtags') }}">
                <div class="tag-wrap" id="tag-wrap">
                    <div id="tag-list"></div>
                    <input type="text" id="tag-text" class="tag-text"
                           placeholder="# 태그 입력 후 Enter" autocomplete="off" maxlength="31">
                </div>
                <p class="tag-hint">예) #서울마라톤 #풀코스 #날씨맑음</p>
            </div>

            <hr class="form-divider">

            <div class="field">
                <label class="field-label">사진 첨부 <span class="field-hint">(선택, 최대 10장)</span></label>
                <input type="file" id="img-input" name="images[]" accept="image/*" multiple style="display:none">
                <div id="img-zone" class="img-zone">
                    <div class="img-zone-ico">📷</div>
                    <div class="img-zone-txt">
                        <strong>클릭</strong>하거나 이미지를 여기에 끌어다 놓으세요
                        <small>JPG · PNG · WEBP · 장당 최대 10MB</small>
                    </div>
                </div>
                <div class="img-toolbar">
                    <button type="button" id="img-add-btn" class="img-add-btn">+ 이미지 추가</button>
                    <span id="img-counter" class="img-counter">0 / 10</span>
                </div>
                <div id="img-preview" class="img-grid"></div>
                @error('images')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">등록하기</button>
            <a href="{{ route('races.show', $race) }}" class="btn-cancel">취소</a>
        </div>
    </form>
</div>

<script>
// ── Finish Time → hidden ─────────────────────────────────
(function () {
    const hEl = document.getElementById('ft-h');
    const mEl = document.getElementById('ft-m');
    const sEl = document.getElementById('ft-s');
    const hidden = document.getElementById('finish-time-hidden');

    function sync() {
        const h = hEl.value.trim(), m = mEl.value.trim(), s = sEl.value.trim();
        if (h !== '' || m !== '' || s !== '') {
            const mm = m.padStart(2, '0'), ss = s.padStart(2, '0');
            hidden.value = `${h || 0}:${mm}:${ss}`;
        } else {
            hidden.value = '';
        }
    }
    [hEl, mEl, sEl].forEach(el => el.addEventListener('input', sync));
})();

// ── Source Tabs ──────────────────────────────────────────
(function () {
    const tabs = document.querySelectorAll('.source-tab');
    const sourceHidden = document.getElementById('source-hidden');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.source-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('panel-' + tab.dataset.panel).classList.add('active');
            sourceHidden.value = tab.dataset.source;
        });
    });
})();

// ── Watch Parse ──────────────────────────────────────────
(function () {
    const zone     = document.getElementById('watch-zone');
    const input    = document.getElementById('watch-img-input');
    const loading  = document.getElementById('parse-loading');
    const errEl    = document.getElementById('parse-error');
    const result   = document.getElementById('parse-result');
    const detail   = document.getElementById('parse-detail');
    const parsedHidden = document.getElementById('parsed-watch-hidden');
    const ftHidden = document.getElementById('finish-time-hidden');
    const ftH = document.getElementById('ft-h');
    const ftM = document.getElementById('ft-m');
    const ftS = document.getElementById('ft-s');

    zone.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--pink)'; });
    zone.addEventListener('dragleave', () => zone.style.borderColor = '');
    zone.addEventListener('drop', e => { e.preventDefault(); zone.style.borderColor = ''; if (e.dataTransfer.files[0]) parse(e.dataTransfer.files[0]); });
    input.addEventListener('change', () => { if (input.files[0]) parse(input.files[0]); input.value = ''; });

    async function parse(file) {
        loading.style.display = 'block';
        errEl.style.display = 'none';
        result.classList.remove('show');

        const fd = new FormData();
        fd.append('watch_image', file);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

        try {
            const res = await fetch('{{ route("reviews.parse-watch") }}', { method: 'POST', body: fd });
            const json = await res.json();
            loading.style.display = 'none';

            if (!json.ok) { errEl.textContent = json.message || '파싱 실패'; errEl.style.display = 'block'; return; }

            const d = json.data;
            parsedHidden.value = JSON.stringify(d);

            // 완주기록 자동 입력
            if (d.finish_time) {
                const parts = d.finish_time.split(':');
                ftH.value = parts[0] || 0; ftM.value = parts[1] || '00'; ftS.value = parts[2] || '00';
                ftHidden.value = d.finish_time;
            }

            // 결과 표시
            const items = [];
            if (d.finish_time) items.push(`<div class="parse-item"><span>완주기록</span><strong>${d.finish_time}</strong></div>`);
            if (d.distance_km) items.push(`<div class="parse-item"><span>거리</span><strong>${d.distance_km}km</strong></div>`);
            if (d.avg_pace_seconds) {
                const pm = Math.floor(d.avg_pace_seconds / 60), ps = d.avg_pace_seconds % 60;
                items.push(`<div class="parse-item"><span>평균페이스</span><strong>${pm}'${String(ps).padStart(2,'0')}"</strong></div>`);
            }
            if (d.avg_heart_rate) items.push(`<div class="parse-item"><span>평균심박</span><strong>${d.avg_heart_rate}bpm</strong></div>`);
            detail.innerHTML = items.join('');
            result.classList.add('show');
        } catch(e) {
            loading.style.display = 'none';
            errEl.textContent = '서버 연결 오류가 발생했습니다.';
            errEl.style.display = 'block';
        }
    }
})();

// ── Hashtag Input ────────────────────────────────────────
(function () {
    const MAX_TAGS = 10, MAX_LEN = 30;
    let tags = [];
    const hidden = document.getElementById('hashtag-hidden');
    const tagList = document.getElementById('tag-list');
    const textIn = document.getElementById('tag-text');
    const wrap = document.getElementById('tag-wrap');
    if (hidden.value) hidden.value.split(',').forEach(t => add(t.trim()));
    function add(name) {
        name = name.replace(/^#+/, '').replace(/\s+/g, '').trim();
        if (!name || tags.length >= MAX_TAGS || name.length > MAX_LEN || tags.includes(name)) return;
        tags.push(name); render();
    }
    function remove(name) { tags = tags.filter(t => t !== name); render(); }
    function render() {
        tagList.innerHTML = tags.map(n => `<span class="tag-chip">#${n}<button type="button" class="tag-chip-rm" data-n="${n}">×</button></span>`).join('');
        hidden.value = tags.join(',');
    }
    tagList.addEventListener('click', e => { if (e.target.classList.contains('tag-chip-rm')) remove(e.target.dataset.n); });
    textIn.addEventListener('keydown', e => {
        if ([' ', ',', 'Enter'].includes(e.key)) { e.preventDefault(); if (textIn.value.trim()) { add(textIn.value); textIn.value = ''; } }
        else if (e.key === 'Backspace' && !textIn.value && tags.length) remove(tags[tags.length - 1]);
    });
    wrap.addEventListener('click', () => textIn.focus());
})();

// ── Image Upload ─────────────────────────────────────────
(function () {
    const MAX = 10, MAX_MB = 10;
    let files = [];
    const input = document.getElementById('img-input');
    const zone = document.getElementById('img-zone');
    const preview = document.getElementById('img-preview');
    const counter = document.getElementById('img-counter');
    const addBtn = document.getElementById('img-add-btn');
    function updateUI() { counter.textContent = files.length + ' / ' + MAX; addBtn.disabled = files.length >= MAX; zone.classList.toggle('full', files.length >= MAX); }
    function addFiles(list) {
        for (const f of list) {
            if (files.length >= MAX) { alert('최대 10장까지 가능합니다.'); break; }
            if (!f.type.startsWith('image/')) { alert(f.name + ': 이미지 파일만 가능합니다.'); continue; }
            if (f.size > MAX_MB * 1024 * 1024) { alert(f.name + ': 10MB 이하만 가능합니다.'); continue; }
            files.push(f);
        }
        renderPreviews();
    }
    function renderPreviews() {
        preview.innerHTML = '';
        files.forEach((f, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div'); item.className = 'pi-item';
                item.innerHTML = `<img src="${e.target.result}" class="pi-img"><button type="button" class="pi-rm" data-idx="${i}">×</button>`;
                preview.appendChild(item);
            };
            reader.readAsDataURL(f);
        });
        const dt = new DataTransfer(); files.forEach(f => dt.items.add(f)); input.files = dt.files;
        updateUI();
    }
    preview.addEventListener('click', e => { if (e.target.classList.contains('pi-rm')) { files.splice(+e.target.dataset.idx, 1); renderPreviews(); } });
    addBtn.addEventListener('click', () => { if (files.length < MAX) input.click(); });
    input.addEventListener('change', () => { addFiles(Array.from(input.files)); input.value = ''; });
    zone.addEventListener('click', () => { if (files.length < MAX) input.click(); });
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag'); addFiles(Array.from(e.dataTransfer.files)); });
    updateUI();
})();
</script>

</body>
</html>
