<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>리뷰 수정 — PAC-RUN</title>
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
        body { background: var(--bg); color: var(--text); font-family: 'Noto Sans KR', sans-serif; font-size: 14px; line-height: 1.6; min-height: 100vh; -webkit-font-smoothing: antialiased; }
        a { text-decoration: none; color: inherit; }

        .h-bar { position: sticky; top: 0; z-index: 200; height: 54px; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; background: rgba(10,10,10,0.92); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid var(--border); }
        .h-logo { font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; letter-spacing: 0.12em; color: var(--accent); }
        .h-logo span { color: var(--text); }
        .h-nav { display: flex; align-items: center; gap: 1.25rem; }
        .h-link { font-size: 0.78rem; font-weight: 500; color: var(--muted); letter-spacing: 0.04em; transition: color 0.15s; }
        .h-link:hover { color: var(--text); }
        .h-btn { padding: 0.35rem 0.9rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; font-family: 'Noto Sans KR', sans-serif; transition: all 0.15s; }
        .h-btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .h-btn-ghost:hover { color: var(--text); border-color: var(--text2); }

        .page-wrap { max-width: 640px; margin: 0 auto; padding: 2.5rem 1.5rem 5rem; }
        .back-link { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--muted); margin-bottom: 2rem; transition: color 0.15s; }
        .back-link:hover { color: var(--text2); }
        .back-link svg { transition: transform 0.15s; }
        .back-link:hover svg { transform: translateX(-2px); }

        .form-header { margin-bottom: 2rem; }
        .form-eyebrow { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent); margin-bottom: 0.4rem; }
        .form-title { font-family: 'Bebas Neue', sans-serif; font-size: 2.2rem; letter-spacing: 0.04em; line-height: 1; color: var(--text); margin-bottom: 0.45rem; }
        .form-subtitle { font-size: 0.82rem; color: var(--muted); }

        .error-box { background: rgba(248,113,113,0.06); border: 1px solid rgba(248,113,113,0.2); border-radius: 8px; padding: 0.9rem 1rem; margin-bottom: 1.5rem; }
        .error-box ul { list-style: none; display: flex; flex-direction: column; gap: 0.3rem; }
        .error-box li { font-size: 0.8rem; color: #F87171; display: flex; gap: 0.4rem; }
        .error-box li::before { content: '·'; flex-shrink: 0; }

        .form-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 1.75rem 2rem; }
        .field { margin-bottom: 1.6rem; }
        .field:last-child { margin-bottom: 0; }
        .field-label { display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.06em; color: var(--text2); margin-bottom: 0.5rem; }
        .field-label-hint { font-size: 0.7rem; color: var(--muted); font-weight: 400; margin-left: 0.4rem; }
        .field-input { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 0.62rem 0.9rem; font-size: 0.88rem; color: var(--text); font-family: 'Noto Sans KR', sans-serif; outline: none; transition: border-color 0.2s; appearance: none; -webkit-appearance: none; }
        .field-input:focus { border-color: var(--accent); }
        .field-input::placeholder { color: var(--muted); }
        .field-input-err { border-color: rgba(248,113,113,0.4) !important; }
        select.field-input { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.2rem; cursor: pointer; }
        select.field-input option { background: var(--surface2); color: var(--text); }
        .field-error { font-size: 0.74rem; color: #F87171; margin-top: 0.4rem; }

        .star-rating { display: flex; gap: 0.35rem; }
        .star-label { cursor: pointer; display: flex; }
        .star-label input { position: absolute; opacity: 0; width: 0; height: 0; }
        .star-glyph { font-size: 2.2rem; line-height: 1; color: #2C2C2C; transition: color 0.12s, transform 0.1s; }
        .star-glyph:hover { transform: scale(1.12); }

        .field-textarea { resize: vertical; min-height: 160px; }
        .char-counter { font-size: 0.7rem; color: var(--muted); text-align: right; margin-top: 0.35rem; }
        .char-counter.warn { color: #F97316; }
        .char-counter.limit { color: #F87171; }

        .form-divider { border: none; border-top: 1px solid var(--border); margin: 1.75rem 0; }

        /* ── HASHTAG INPUT ── */
        .tag-wrap { border: 1px solid var(--border); border-radius: 8px; background: var(--bg); padding: 0.4rem 0.65rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; cursor: text; min-height: 42px; transition: border-color 0.2s; }
        .tag-wrap:focus-within { border-color: var(--accent); }
        .tag-chip { display: inline-flex; align-items: center; gap: 0.25rem; background: rgba(255,107,53,0.1); border: 1px solid rgba(255,107,53,0.28); color: var(--accent); border-radius: 4px; padding: 0.18rem 0.5rem; font-size: 0.78rem; font-weight: 500; }
        .tag-chip-rm { background: none; border: none; color: var(--accent); cursor: pointer; padding: 0 0 0 0.1rem; font-size: 0.8rem; line-height: 1; opacity: 0.65; }
        .tag-chip-rm:hover { opacity: 1; }
        .tag-text { border: none; background: transparent; color: var(--text); font-size: 0.85rem; outline: none; font-family: 'Noto Sans KR', sans-serif; min-width: 130px; flex: 1; padding: 0.1rem 0; }
        .tag-text::placeholder { color: var(--muted); font-size: 0.8rem; }
        .tag-hint { font-size: 0.7rem; color: var(--muted); margin-top: 0.3rem; }

        /* ── IMAGE UPLOAD ── */
        .img-zone { border: 2px dashed var(--border); border-radius: 10px; padding: 1.2rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
        .img-zone:hover, .img-zone.drag { border-color: var(--accent); background: rgba(255,107,53,0.04); }
        .img-zone.full { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
        .img-zone-ico { font-size: 1.6rem; margin-bottom: 0.3rem; }
        .img-zone-txt { font-size: 0.8rem; color: var(--muted); }
        .img-zone-txt strong { color: var(--accent); }
        .img-zone-txt small { display: block; font-size: 0.7rem; margin-top: 0.15rem; color: #555; }
        .img-toolbar { display: flex; align-items: center; justify-content: space-between; margin-top: 0.6rem; }
        .img-add-btn { padding: 0.3rem 0.75rem; background: transparent; border: 1px solid var(--border); color: var(--text2); border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.15s; font-family: 'Noto Sans KR', sans-serif; }
        .img-add-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); }
        .img-add-btn:disabled { opacity: 0.38; cursor: not-allowed; }
        .img-counter { font-size: 0.72rem; color: var(--muted); }
        .img-counter.full { color: var(--accent); }
        .img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(76px, 1fr)); gap: 0.45rem; margin-top: 0.6rem; }
        .ei-item, .pi-item { position: relative; }
        .ei-img, .pi-img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); display: block; }
        .ei-rm, .pi-rm { position: absolute; top: 3px; right: 3px; width: 19px; height: 19px; background: rgba(0,0,0,0.72); color: #fff; border: none; border-radius: 50%; font-size: 0.7rem; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
        .ei-rm:hover, .pi-rm:hover { background: #E85520; }
        .ei-label { position: absolute; bottom: 3px; left: 3px; font-size: 0.55rem; background: rgba(0,0,0,0.6); color: var(--text2); padding: 1px 4px; border-radius: 3px; pointer-events: none; }

        .btn-row { display: flex; gap: 0.75rem; align-items: center; }
        .btn-submit { padding: 0.62rem 1.6rem; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Noto Sans KR', sans-serif; transition: background 0.15s; }
        .btn-submit:hover { background: var(--accent-d); }
        .btn-cancel { padding: 0.62rem 1.2rem; background: transparent; color: var(--muted); border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: all 0.15s; }
        .btn-cancel:hover { color: var(--text2); border-color: var(--text2); }

        @media (max-width: 580px) {
            .page-wrap { padding: 1.5rem 1rem 3rem; }
            .form-card { padding: 1.4rem 1.25rem; }
            .h-nav .h-link { display: none; }
        }
    </style>
</head>
<body>

<header class="h-bar">
    <a href="{{ route('home') }}" class="h-logo">PAC<span>-RUN</span></a>
    <nav class="h-nav">
        <a href="{{ route('races.index') }}" class="h-link">대회</a>
        @auth
            <a href="{{ route('dashboard') }}" class="h-link">대시보드</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="h-btn h-btn-ghost">로그아웃</button>
            </form>
        @endauth
    </nav>
</header>

<div class="page-wrap">
    <a href="{{ route('races.show', $review->race_id) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        대회 상세로
    </a>

    <div class="form-header">
        <p class="form-eyebrow">Edit Review</p>
        <h1 class="form-title">리뷰 수정</h1>
        <p class="form-subtitle">{{ $review->race->name ?? '' }}</p>
    </div>

    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('reviews.update', $review) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Edition (참가 연도) --}}
            @if(isset($editions) && $editions->isNotEmpty())
            <div class="field">
                <label class="field-label">참가 연도
                    <span class="field-label-hint">(선택)</span>
                </label>
                <select name="race_edition_id" class="field-input">
                    <option value="">연도 선택 안 함</option>
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}"
                            @selected(old('race_edition_id', $review->race_edition_id) == $ed->id)>
                            {{ $ed->year }}년
                            @if($ed->race_date) ({{ $ed->race_date->format('Y.m.d') }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('race_edition_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <hr class="form-divider">
            @endif

            {{-- Distance --}}
            <div class="field">
                <label class="field-label">참가 거리</label>
                <select name="distance" class="field-input {{ $errors->has('distance') ? 'field-input-err' : '' }}">
                    <option value="">선택해주세요</option>
                    @php
                        $raceDistances = array_map('trim', $review->race->distances ?? []);
                        $options       = !empty($raceDistances)
                            ? $raceDistances
                            : ['5K', '10K', '하프', '풀', '기타'];
                        $currentDist   = old('distance', $review->distance);
                        // 기존에 저장된 값이 목록에 없으면 포함 (이전 데이터 호환)
                        if ($currentDist && !in_array($currentDist, $options)) {
                            $options[] = $currentDist;
                        }
                    @endphp
                    @foreach($options as $d)
                        <option value="{{ $d }}" {{ $currentDist === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
                @error('distance')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <hr class="form-divider">

            {{-- Star Rating --}}
            <div class="field" x-data="{ rating: {{ old('rating', $review->rating) }} }">
                <label class="field-label">평점</label>
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="star-label">
                            <input type="radio" name="rating" value="{{ $i }}"
                                   x-on:change="rating = {{ $i }}"
                                   {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                            <span class="star-glyph"
                                  :style="rating >= {{ $i }} ? 'color:var(--star)' : 'color:#2C2C2C'">★</span>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <hr class="form-divider">

            {{-- Content --}}
            <div class="field" x-data="{ len: {{ strlen(old('content', $review->content ?? '')) }} }">
                <label class="field-label">
                    리뷰 내용
                    <span class="field-label-hint">(최소 10자, 최대 2000자)</span>
                </label>
                <textarea name="content" rows="8"
                          class="field-input field-textarea {{ $errors->has('content') ? 'field-input-err' : '' }}"
                          placeholder="대회 코스, 분위기, 운영, 기념품 등 솔직한 후기를 자유롭게 작성해주세요."
                          @input="len = $event.target.value.length"
                          maxlength="2000">{{ old('content', $review->content) }}</textarea>
                <p class="char-counter"
                   :class="len >= 1900 ? 'limit' : len >= 1500 ? 'warn' : ''"
                   x-text="`${len} / 2000`"></p>
                @error('content')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <hr class="form-divider">

            {{-- Hashtags --}}
            <div class="field">
                <label class="field-label">
                    해시태그
                    <span class="field-label-hint">(선택, 최대 10개 · 30자 이내)</span>
                </label>
                @php
                    $existingTagStr = old('hashtags',
                        $review->hashtags->pluck('name')->implode(',')
                    );
                @endphp
                <input type="hidden" name="hashtags" id="hashtag-hidden" value="{{ $existingTagStr }}">
                <div class="tag-wrap" id="tag-wrap">
                    <div id="tag-list"></div>
                    <input type="text" id="tag-text" class="tag-text"
                           placeholder="# 태그 입력 후 Enter 또는 Space"
                           autocomplete="off" maxlength="31">
                </div>
                <p class="tag-hint">예) #서울마라톤 #풀코스 #날씨맑음 — Enter · Space · 쉼표로 추가</p>
            </div>

            <hr class="form-divider">

            {{-- Image Upload --}}
            <div class="field">
                <label class="field-label">
                    사진 첨부
                    <span class="field-label-hint">(선택, 최대 10장 · 장당 10MB)</span>
                </label>
                <input type="file" id="img-input" name="images[]" accept="image/*" multiple style="display:none">

                @php
                    $existingPaths = $review->image_urls ?? [];
                    $existingUrls  = $review->getImageUrls();
                @endphp

                {{-- 기존 이미지 목록 --}}
                @if(!empty($existingPaths))
                    <div id="ei-grid" class="img-grid" style="margin-bottom:0.5rem">
                        @foreach($existingPaths as $i => $path)
                            <div class="ei-item">
                                <input type="hidden" name="existing_images[]" value="{{ $path }}">
                                <img src="{{ $existingUrls[$i] ?? '' }}" class="ei-img" alt="기존 이미지">
                                <span class="ei-label">기존</span>
                                <button type="button" class="ei-rm" title="삭제">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div id="img-zone" class="img-zone">
                    <div class="img-zone-ico">📷</div>
                    <div class="img-zone-txt">
                        <strong>클릭</strong>하거나 이미지를 여기에 끌어다 놓으세요
                        <small>JPG · PNG · WEBP · GIF · 장당 최대 10MB</small>
                    </div>
                </div>

                <div class="img-toolbar">
                    <button type="button" id="img-add-btn" class="img-add-btn">+ 이미지 추가</button>
                    <span id="img-counter" class="img-counter">0 / 10</span>
                </div>

                <div id="img-preview" class="img-grid"></div>

                @error('images') <p class="field-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-submit">수정 완료</button>
                <a href="{{ route('races.show', $review->race_id) }}" class="btn-cancel">취소</a>
            </div>
        </form>
    </div>
</div>

<script>
// ── Hashtag Input ──────────────────────────────────────────
(function () {
    const MAX_TAGS = 10, MAX_LEN = 30;
    let tags = [];

    const hidden  = document.getElementById('hashtag-hidden');
    const tagList = document.getElementById('tag-list');
    const textIn  = document.getElementById('tag-text');
    const wrap    = document.getElementById('tag-wrap');

    // 기존 태그 로드
    if (hidden.value) hidden.value.split(',').forEach(t => add(t.trim()));

    function add(name) {
        name = name.replace(/^#+/, '').replace(/\s+/g, '').trim();
        if (!name || tags.length >= MAX_TAGS || name.length > MAX_LEN || tags.includes(name)) return;
        tags.push(name);
        render();
    }
    function remove(name) { tags = tags.filter(t => t !== name); render(); }
    function render() {
        tagList.innerHTML = tags.map(n =>
            `<span class="tag-chip">#${n}<button type="button" class="tag-chip-rm" data-n="${n}">×</button></span>`
        ).join('');
        hidden.value = tags.join(',');
    }

    tagList.addEventListener('click', e => {
        if (e.target.classList.contains('tag-chip-rm')) remove(e.target.dataset.n);
    });
    textIn.addEventListener('keydown', e => {
        if ([' ', ',', 'Enter'].includes(e.key)) {
            e.preventDefault();
            if (textIn.value.trim()) { add(textIn.value); textIn.value = ''; }
        } else if (e.key === 'Backspace' && !textIn.value && tags.length) {
            remove(tags[tags.length - 1]);
        }
    });
    wrap.addEventListener('click', () => textIn.focus());
})();

// ── Image Upload ───────────────────────────────────────────
(function () {
    const MAX = 10, MAX_MB = 10;
    let files = [];

    const input   = document.getElementById('img-input');
    const zone    = document.getElementById('img-zone');
    const preview = document.getElementById('img-preview');
    const counter = document.getElementById('img-counter');
    const addBtn  = document.getElementById('img-add-btn');

    function existingKept() {
        return document.querySelectorAll('#ei-grid .ei-item input[type=hidden]').length;
    }
    function total() { return existingKept() + files.length; }
    function available() { return MAX - total(); }

    function updateUI() {
        counter.textContent = total() + ' / ' + MAX;
        counter.className = 'img-counter' + (total() >= MAX ? ' full' : '');
        addBtn.disabled = available() <= 0;
        zone.classList.toggle('full', available() <= 0);
    }

    function addFiles(list) {
        for (const f of list) {
            if (available() <= 0) { alert('최대 10장까지 업로드할 수 있습니다.'); break; }
            if (!f.type.startsWith('image/')) { alert(f.name + ': 이미지 파일만 가능합니다.'); continue; }
            if (f.size > MAX_MB * 1024 * 1024) { alert(f.name + ': 10MB 이하 파일만 업로드 가능합니다.'); continue; }
            files.push(f);
        }
        renderNewPreviews();
    }

    function renderNewPreviews() {
        preview.innerHTML = '';
        files.forEach((f, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div');
                item.className = 'pi-item';
                item.innerHTML = '<img src="' + e.target.result + '" class="pi-img">'
                    + '<button type="button" class="pi-rm" data-idx="' + i + '" title="삭제">×</button>';
                preview.appendChild(item);
            };
            reader.readAsDataURL(f);
        });
        syncInput();
        updateUI();
    }

    function syncInput() {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    // 새 이미지 삭제
    preview.addEventListener('click', e => {
        if (e.target.classList.contains('pi-rm')) {
            files.splice(+e.target.dataset.idx, 1);
            renderNewPreviews();
        }
    });

    // 기존 이미지 삭제 (hidden input 제거 → 서버에서 파일 삭제)
    document.addEventListener('click', e => {
        if (e.target.classList.contains('ei-rm')) {
            e.target.closest('.ei-item').remove();
            updateUI();
        }
    });

    addBtn.addEventListener('click', () => { if (available() > 0) input.click(); });
    input.addEventListener('change', () => { addFiles(Array.from(input.files)); input.value = ''; });
    zone.addEventListener('click', () => { if (available() > 0) input.click(); });
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag'); addFiles(Array.from(e.dataTransfer.files)); });

    updateUI();
})();
</script>

</body>
</html>
