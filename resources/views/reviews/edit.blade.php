@extends('layouts.review')
@section('title', '리뷰 수정 — PAC-RUN')

@push('styles')
<style>
    :root { --bg:#F7F8FA; --card:#FFFFFF; --line:#E8EAEE; --ink:#16181D; --ink-2:#5A6170; --ink-3:#9AA1AE; --pink:#E80043; --pink-soft:#FFF0F4; --star:#F59E0B; }

    .page-wrap { max-width: 640px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }
    .back-link { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: var(--ink-3); margin-bottom: 1.5rem; transition: color 0.15s; }
    .back-link:hover { color: var(--ink-2); }
    .back-link:hover svg { transform: translateX(-2px); }
    .back-link svg { transition: transform 0.15s; }
    .form-header { margin-bottom: 1.75rem; }
    .form-eyebrow { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: var(--pink); margin-bottom: 0.35rem; font-family: 'Archivo', sans-serif; }
    .form-title { font-size: 1.65rem; font-weight: 700; color: var(--ink); line-height: 1.2; margin-bottom: 0.3rem; }
    .form-subtitle { font-size: 0.85rem; color: var(--ink-2); }
    .error-box { background: #FFF5F5; border: 1px solid #FFC5C5; border-radius: 8px; padding: 0.9rem 1rem; margin-bottom: 1.5rem; }
    .error-box ul { list-style: none; }
    .error-box li { font-size: 0.8rem; color: #C53030; display: flex; gap: 0.4rem; }
    .error-box li::before { content: '·'; }
    .form-card { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 1.75rem; margin-bottom: 1rem; }
    .card-title { font-size: 0.78rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 1.25rem; font-family: 'Archivo', sans-serif; }
    .field { margin-bottom: 1.4rem; }
    .field:last-child { margin-bottom: 0; }
    .field-label { display: block; font-size: 0.78rem; font-weight: 600; color: var(--ink-2); margin-bottom: 0.45rem; }
    .field-hint { font-size: 0.72rem; color: var(--ink-3); font-weight: 400; margin-left: 0.35rem; }
    .field-input { width: 100%; background: var(--bg); border: 1px solid var(--line); border-radius: 8px; padding: 0.65rem 0.9rem; font-size: 0.9rem; color: var(--ink); font-family: 'Pretendard', sans-serif; outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
    .field-input:focus { border-color: var(--pink); }
    .field-input::placeholder { color: var(--ink-3); }
    .field-error { font-size: 0.75rem; color: var(--pink); margin-top: 0.35rem; }
    .form-divider { border: none; border-top: 1px solid var(--line); margin: 1.5rem 0; }
    select.field-input { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA1AE' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.2rem; cursor: pointer; }
    .seg-group { display: flex; gap: 0.5rem; }
    .seg-item { flex: 1; }
    .seg-item input { position: absolute; opacity: 0; width: 0; height: 0; }
    .seg-btn { display: block; text-align: center; padding: 0.6rem 0.5rem; border: 1.5px solid var(--line); border-radius: 8px; font-size: 0.82rem; font-weight: 600; color: var(--ink-2); cursor: pointer; transition: all 0.15s; background: var(--bg); }
    .seg-item input:checked + .seg-btn { border-color: var(--pink); color: var(--pink); background: var(--pink-soft); }
    .seg-sub { font-size: 0.65rem; font-weight: 400; display: block; margin-top: 0.1rem; opacity: 0.7; font-family: 'Archivo', sans-serif; }
    .time-row { display: flex; align-items: center; gap: 0.5rem; }
    .time-input { width: 64px; text-align: center; font-family: 'Archivo', sans-serif; font-size: 1.1rem; font-weight: 600; color: var(--ink); background: var(--bg); border: 1.5px solid var(--line); border-radius: 8px; padding: 0.55rem 0.5rem; outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
    .time-input:focus { border-color: var(--pink); }
    .time-sep { font-size: 1.2rem; font-weight: 700; color: var(--ink-3); font-family: 'Archivo', sans-serif; }
    .time-label { font-size: 0.7rem; color: var(--ink-3); text-align: center; margin-top: 0.2rem; }
    .star-rating { display: flex; gap: 0.25rem; }
    .star-label { cursor: pointer; display: flex; }
    .star-label input { position: absolute; opacity: 0; width: 0; height: 0; }
    .star-glyph { font-size: 2.2rem; line-height: 1; color: var(--line); transition: color 0.12s, transform 0.1s; }
    .star-glyph:hover { transform: scale(1.1); }
    .field-textarea { resize: vertical; min-height: 160px; }
    .char-counter { font-size: 0.72rem; color: var(--ink-3); text-align: right; margin-top: 0.3rem; font-family: 'Archivo', sans-serif; }
    .char-counter.warn { color: #D97706; }
    .char-counter.limit { color: var(--pink); }
    .tag-wrap { border: 1px solid var(--line); border-radius: 8px; background: var(--bg); padding: 0.4rem 0.65rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; cursor: text; min-height: 42px; transition: border-color 0.2s; }
    .tag-wrap:focus-within { border-color: var(--pink); }
    .tag-chip { display: inline-flex; align-items: center; gap: 0.25rem; background: var(--pink-soft); border: 1px solid #FFC5D5; color: var(--pink); border-radius: 4px; padding: 0.18rem 0.5rem; font-size: 0.78rem; font-weight: 500; }
    .tag-chip-rm { background: none; border: none; color: var(--pink); cursor: pointer; padding: 0 0 0 0.1rem; font-size: 0.8rem; opacity: 0.65; }
    .tag-chip-rm:hover { opacity: 1; }
    .tag-text { border: none; background: transparent; color: var(--ink); font-size: 0.85rem; outline: none; font-family: 'Pretendard', sans-serif; min-width: 130px; flex: 1; padding: 0.1rem 0; }
    .tag-text::placeholder { color: var(--ink-3); font-size: 0.8rem; }
    .tag-hint { font-size: 0.72rem; color: var(--ink-3); margin-top: 0.25rem; }
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
    .ei-item, .pi-item { position: relative; }
    .ei-img, .pi-img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 6px; border: 1px solid var(--line); display: block; }
    .ei-rm, .pi-rm { position: absolute; top: 3px; right: 3px; width: 20px; height: 20px; background: rgba(0,0,0,0.55); color: #fff; border: none; border-radius: 50%; font-size: 0.7rem; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
    .ei-rm:hover, .pi-rm:hover { background: var(--pink); }
    .ei-label { position: absolute; bottom: 3px; left: 3px; font-size: 0.55rem; background: rgba(0,0,0,0.5); color: #fff; padding: 1px 4px; border-radius: 3px; pointer-events: none; }
    .btn-row { display: flex; gap: 0.75rem; align-items: center; margin-top: 1.5rem; }
    .btn-submit { padding: 0.7rem 1.8rem; background: var(--pink); color: #fff; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: opacity 0.15s; }
    .btn-submit:hover { opacity: 0.88; }
    .btn-cancel { padding: 0.7rem 1.2rem; background: transparent; color: var(--ink-2); border: 1px solid var(--line); border-radius: 8px; font-size: 0.88rem; font-weight: 500; transition: all 0.15s; }
    .btn-cancel:hover { color: var(--ink); border-color: var(--ink-3); }
    @media (max-width: 580px) { .page-wrap { padding: 1.25rem 1rem 3rem; } .form-card { padding: 1.25rem; } }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <a href="{{ route('races.show', $race) }}" class="back-link">
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
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.update', $review) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ① 참가 정보 --}}
        <div class="form-card">
            <p class="card-title">① 참가 정보</p>

            @if(isset($editions) && $editions->isNotEmpty())
            <div class="field">
                <label class="field-label">참가 연도 <span class="field-hint">(선택)</span></label>
                <select name="race_edition_id" class="field-input">
                    <option value="">연도 선택 안 함</option>
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" @selected(old('race_edition_id', $review->race_edition_id) == $ed->id)>
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
                                   @checked(old('course_type', $review->course_type) === $val)>
                            <span class="seg-btn">{{ $label }}<span class="seg-sub">{{ $sub }}</span></span>
                        </label>
                    @endforeach
                </div>
                @error('course_type')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- 참가 거리 --}}
            <div class="field">
                <label class="field-label">참가 거리 <span style="color:var(--pink)">*</span></label>
                <select name="distance" class="field-input">
                    <option value="">선택해주세요</option>
                    @php
                        $raceDistances = array_map('trim', $review->race->distances ?? []);
                        $options = !empty($raceDistances) ? $raceDistances : ['5K','10K','하프','풀','기타'];
                    @endphp
                    @foreach($options as $d)
                        <option value="{{ $d }}" @selected(old('distance', $review->distance) === $d)>{{ $d }}</option>
                    @endforeach
                </select>
                @error('distance')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- 완주기록 --}}
            <div class="field">
                <label class="field-label">완주기록 <span class="field-hint">(선택 — H:MM:SS)</span></label>
                @php
                    $ft = old('finish_time', $review->finish_time ?? '');
                    $ftParts = $ft ? explode(':', $ft) : ['', '', ''];
                @endphp
                <input type="hidden" name="finish_time" id="finish-time-hidden" value="{{ $ft }}">
                <div class="time-row">
                    <div>
                        <input type="number" id="ft-h" class="time-input" placeholder="0" min="0" max="99" value="{{ $ftParts[0] ?? '' }}">
                        <div class="time-label">시간</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" id="ft-m" class="time-input" placeholder="00" min="0" max="59" value="{{ $ftParts[1] ?? '' }}">
                        <div class="time-label">분</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" id="ft-s" class="time-input" placeholder="00" min="0" max="59" value="{{ $ftParts[2] ?? '' }}">
                        <div class="time-label">초</div>
                    </div>
                </div>
                @error('finish_time')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- source --}}
            <input type="hidden" name="source" value="{{ old('source', $review->source ?? 'manual') }}">
            <input type="hidden" name="parsed_watch_data" value="{{ old('parsed_watch_data', $review->parsed_watch_data ? json_encode($review->parsed_watch_data) : '') }}">
        </div>

        {{-- ② 평점 + 본문 --}}
        <div class="form-card">
            <p class="card-title">② 후기 작성</p>

            <div class="field" x-data="{ rating: {{ old('rating', $review->rating) }} }">
                <label class="field-label">평점 <span style="color:var(--pink)">*</span></label>
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="star-label">
                            <input type="radio" name="rating" value="{{ $i }}"
                                   x-on:change="rating = {{ $i }}"
                                   @checked(old('rating', $review->rating) == $i)>
                            <span class="star-glyph" :style="rating >= {{ $i }} ? 'color:var(--star)' : ''">★</span>
                        </label>
                    @endfor
                </div>
                @error('rating')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <hr class="form-divider">

            <div class="field" x-data="{ len: {{ strlen(old('content', $review->content)) }} }">
                <label class="field-label">
                    리뷰 내용 <span style="color:var(--pink)">*</span>
                    <span class="field-hint">(최소 10자, 최대 2000자)</span>
                </label>
                <textarea name="content" rows="8"
                          class="field-input field-textarea"
                          placeholder="코스, 분위기, 운영, 기념품 등 솔직한 후기를 작성해주세요."
                          @input="len = $event.target.value.length"
                          maxlength="2000">{{ old('content', $review->content) }}</textarea>
                <p class="char-counter" :class="len >= 1900 ? 'limit' : len >= 1500 ? 'warn' : ''"
                   x-text="`${len} / 2000`"></p>
                @error('content')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ③ 태그 + 사진 --}}
        <div class="form-card">
            <p class="card-title">③ 태그 &amp; 사진</p>

            <div class="field">
                <label class="field-label">해시태그 <span class="field-hint">(선택, 최대 10개)</span></label>
                @php
                    $existingTags = old('hashtags') ?? ($review->hashtags->pluck('name')->implode(','));
                @endphp
                <input type="hidden" name="hashtags" id="hashtag-hidden" value="{{ $existingTags }}">
                <div class="tag-wrap" id="tag-wrap">
                    <div id="tag-list"></div>
                    <input type="text" id="tag-text" class="tag-text" placeholder="# 태그 입력 후 Enter" autocomplete="off" maxlength="31">
                </div>
                <p class="tag-hint">예) #서울마라톤 #풀코스 #날씨맑음</p>
            </div>

            <hr class="form-divider">

            <div class="field">
                <label class="field-label">사진 첨부 <span class="field-hint">(선택, 최대 10장)</span></label>
                <input type="file" id="img-input" name="images[]" accept="image/*" multiple style="display:none">

                {{-- 기존 이미지 --}}
                @php $existingUrls = $review->getImageUrls(); @endphp
                @if(!empty($existingUrls))
                    <div id="existing-preview" class="img-grid" style="margin-bottom:0.6rem">
                        @foreach($existingUrls as $idx => $url)
                            <div class="ei-item" id="ei-{{ $idx }}">
                                <img src="{{ $url }}" class="ei-img" loading="lazy">
                                <button type="button" class="ei-rm" data-idx="{{ $idx }}" data-path="{{ $review->image_urls[$idx] ?? '' }}">×</button>
                                <input type="hidden" name="existing_images[]" value="{{ $review->image_urls[$idx] ?? '' }}" id="ei-inp-{{ $idx }}">
                                <span class="ei-label">기존</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div id="img-zone" class="img-zone">
                    <div class="img-zone-ico">📷</div>
                    <div class="img-zone-txt">
                        <strong>클릭</strong>하거나 이미지를 여기에 끌어다 놓으세요
                        <small>JPG · PNG · WEBP · 장당 최대 10MB</small>
                    </div>
                </div>
                <div class="img-toolbar">
                    <button type="button" id="img-add-btn" class="img-add-btn">+ 이미지 추가</button>
                    <span id="img-counter" class="img-counter">{{ count($existingUrls) }} / 10</span>
                </div>
                <div id="img-preview" class="img-grid"></div>
                @error('images')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">수정하기</button>
            <a href="{{ route('races.show', $race) }}" class="btn-cancel">취소</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Finish Time ───────────────────────────────────────────
(function () {
    const hEl = document.getElementById('ft-h'), mEl = document.getElementById('ft-m'), sEl = document.getElementById('ft-s');
    const hidden = document.getElementById('finish-time-hidden');
    function sync() {
        const h = hEl.value.trim(), m = mEl.value.trim(), s = sEl.value.trim();
        hidden.value = (h || m || s) ? `${h || 0}:${m.padStart(2,'0')}:${s.padStart(2,'0')}` : '';
    }
    [hEl, mEl, sEl].forEach(el => el.addEventListener('input', sync));
})();

// ── Hashtag ───────────────────────────────────────────────
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

// ── Image Upload ──────────────────────────────────────────
(function () {
    const MAX = 10, MAX_MB = 10;
    let existingCount = document.querySelectorAll('.ei-item').length;
    let newFiles = [];
    const input = document.getElementById('img-input');
    const zone = document.getElementById('img-zone');
    const preview = document.getElementById('img-preview');
    const counter = document.getElementById('img-counter');
    const addBtn = document.getElementById('img-add-btn');

    document.getElementById('existing-preview')?.addEventListener('click', e => {
        if (e.target.classList.contains('ei-rm')) {
            const idx = e.target.dataset.idx;
            document.getElementById(`ei-${idx}`)?.remove();
            document.getElementById(`ei-inp-${idx}`)?.remove();
            existingCount = document.querySelectorAll('.ei-item').length;
            updateUI();
        }
    });

    function total() { return existingCount + newFiles.length; }
    function updateUI() { counter.textContent = total() + ' / ' + MAX; addBtn.disabled = total() >= MAX; zone.classList.toggle('full', total() >= MAX); }
    function addFiles(list) {
        for (const f of list) {
            if (total() >= MAX) { alert('최대 10장까지 가능합니다.'); break; }
            if (!f.type.startsWith('image/')) { alert(f.name + ': 이미지 파일만 가능합니다.'); continue; }
            if (f.size > MAX_MB * 1024 * 1024) { alert(f.name + ': 10MB 이하만 가능합니다.'); continue; }
            newFiles.push(f);
        }
        renderPreviews();
    }
    function renderPreviews() {
        preview.innerHTML = '';
        newFiles.forEach((f, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div'); item.className = 'pi-item';
                item.innerHTML = `<img src="${e.target.result}" class="pi-img"><button type="button" class="pi-rm" data-idx="${i}">×</button>`;
                preview.appendChild(item);
            };
            reader.readAsDataURL(f);
        });
        const dt = new DataTransfer(); newFiles.forEach(f => dt.items.add(f)); input.files = dt.files;
        updateUI();
    }
    preview.addEventListener('click', e => { if (e.target.classList.contains('pi-rm')) { newFiles.splice(+e.target.dataset.idx, 1); renderPreviews(); } });
    addBtn.addEventListener('click', () => { if (total() < MAX) input.click(); });
    input.addEventListener('change', () => { addFiles(Array.from(input.files)); input.value = ''; });
    zone.addEventListener('click', () => { if (total() < MAX) input.click(); });
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag'); addFiles(Array.from(e.dataTransfer.files)); });
    updateUI();
})();
</script>
@endpush
