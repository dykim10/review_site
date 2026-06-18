@extends('layouts.review')

@section('title', '레이스 플랜 생성 — ' . $race->name . ' — PAC-RUN')

@push('styles')
<style>
    :root { --bg:#0A0A0A; --s:#141414; --s2:#1C1C1C; --bd:#242424; --acc:#FF6B35; --acc-d:#E85520; --text:#E8E8E8; --t2:#AAAAAA; --mut:#666; }
    .page-wrap { max-width: 600px; margin: 0 auto; padding: 2.5rem 1.5rem 5rem; }
    .back-link { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--mut); margin-bottom: 1.5rem; transition: color 0.15s; }
    .back-link:hover { color: var(--t2); }
    .back-link svg { transition: transform 0.15s; }
    .back-link:hover svg { transform: translateX(-2px); }
    .form-eyebrow { font-family: 'Archivo', sans-serif; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--acc); margin-bottom: 0.3rem; }
    .form-title { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: 2.5rem; letter-spacing: 0.06em; color: #16181D; line-height: 1; margin-bottom: 0.25rem; }
    .form-sub { font-size: 0.82rem; color: #5A6170; margin-bottom: 2rem; }
    .alert-error { background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); color: #F87171; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.82rem; margin-bottom: 1.25rem; }
    .card { background: var(--s); border: 1px solid var(--bd); border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; }
    .card-label { font-family: 'Archivo', sans-serif; font-size: 0.62rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: var(--mut); margin-bottom: 1.25rem; padding-bottom: 0.65rem; border-bottom: 1px solid var(--bd); }
    .field { margin-bottom: 1.3rem; }
    .field:last-child { margin-bottom: 0; }
    .fl { display: block; font-size: 0.75rem; font-weight: 600; color: var(--t2); margin-bottom: 0.4rem; }
    .fl .req { color: var(--acc); }
    .fl .hint { font-weight: 400; color: var(--mut); font-size: 0.68rem; margin-left: 0.3rem; }
    .fi { width: 100%; background: var(--bg); border: 1px solid var(--bd); border-radius: 8px; padding: 0.6rem 0.85rem; font-size: 0.88rem; color: var(--text); font-family: 'Pretendard', sans-serif; outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
    .fi:focus { border-color: var(--acc); }
    select.fi { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.2rem; cursor: pointer; }
    option { background: var(--s2); }
    .seg-group { display: flex; gap: 0.5rem; }
    .seg-item { flex: 1; }
    .seg-item input { position: absolute; opacity: 0; width: 0; height: 0; }
    .seg-btn { display: block; text-align: center; padding: 0.6rem; border: 1.5px solid var(--bd); border-radius: 8px; font-size: 0.8rem; font-weight: 600; color: var(--mut); cursor: pointer; transition: all 0.15s; }
    .seg-sub { font-size: 0.62rem; font-family: 'Archivo', sans-serif; display: block; margin-top: 0.1rem; opacity: 0.7; }
    .seg-item input:checked + .seg-btn { border-color: var(--acc); color: var(--acc); background: rgba(255,107,53,0.08); }
    .time-row { display: flex; align-items: center; gap: 0.5rem; }
    .time-inp { width: 64px; text-align: center; font-family: 'Archivo', sans-serif; font-size: 1.1rem; font-weight: 600; background: var(--bg); border: 1.5px solid var(--bd); border-radius: 8px; padding: 0.55rem 0.5rem; color: var(--text); outline: none; transition: border-color 0.2s; -webkit-appearance: none; }
    .time-inp:focus { border-color: var(--acc); }
    .time-sep { font-size: 1.2rem; font-weight: 700; color: var(--mut); font-family: 'Archivo', sans-serif; }
    .time-label { font-size: 0.65rem; color: var(--mut); text-align: center; margin-top: 0.15rem; }
    .radio-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .radio-card input { position: absolute; opacity: 0; width: 0; height: 0; }
    .radio-body { display: block; padding: 0.75rem 1rem; border: 1.5px solid var(--bd); border-radius: 8px; cursor: pointer; transition: all 0.15s; }
    .radio-title { font-size: 0.82rem; font-weight: 600; color: var(--t2); }
    .radio-desc { font-size: 0.7rem; color: var(--mut); margin-top: 0.15rem; }
    .radio-card input:checked + .radio-body { border-color: var(--acc); background: rgba(255,107,53,0.06); }
    .radio-card input:checked + .radio-body .radio-title { color: var(--acc); }
    .btn-submit { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.85rem; background: var(--acc); color: #fff; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: background 0.15s; margin-top: 1.25rem; }
    .btn-submit:hover { background: var(--acc-d); }
    .btn-submit:disabled { opacity: 0.5; cursor: wait; }
    .loading-overlay { display: none; position: fixed; inset: 0; background: rgba(10,10,10,0.85); backdrop-filter: blur(8px); z-index: 999; flex-direction: column; align-items: center; justify-content: center; gap: 1.25rem; }
    .loading-overlay.active { display: flex; }
    .spinner { width: 44px; height: 44px; border: 3px solid var(--bd); border-top-color: var(--acc); border-radius: 50%; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: 1.3rem; letter-spacing: 0.1em; color: var(--t2); }
    .loading-sub { font-size: 0.78rem; color: var(--mut); }
    @media (max-width: 560px) { .radio-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')
<div class="loading-overlay" id="loading">
    <div class="spinner"></div>
    <div class="loading-text">레이스 플랜 생성 중</div>
    <div class="loading-sub">AI가 코스 · 날씨 · 유사 케이스를 분석하고 있습니다 (10~20초)</div>
</div>

<div class="page-wrap">
    <a href="{{ route('races.show', $race) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ $race->name }}
    </a>

    <p class="form-eyebrow">Race Plan Generator</p>
    <h1 class="form-title">레이스 플랜 생성</h1>
    <p class="form-sub">목표 기록과 훈련 상태를 입력하면 AI가 구간별 페이스 전략을 만들어 드립니다.</p>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $e)<div>· {{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('race-plan.generate', $race) }}" id="plan-form">
        @csrf

        {{-- ① 대회 선택 --}}
        <div class="card">
            <div class="card-label">① 대회 선택</div>
            <div class="field">
                <label class="fl">참가 연도 <span class="req">*</span></label>
                <select name="race_edition_id" class="fi" required>
                    <option value="">선택해주세요</option>
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" @selected(old('race_edition_id') == $ed->id)>
                            {{ $ed->year }}년@if($ed->race_date) · {{ \Carbon\Carbon::parse($ed->race_date)->format('m.d') }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="fl">코스 타입 <span class="req">*</span></label>
                <div class="seg-group">
                    @foreach($courseTypes as $val => $label)
                        @php [$name, $sub] = explode(' (', rtrim($label, ')'), 2) + ['', '']; @endphp
                        <label class="seg-item">
                            <input type="radio" name="course_type" value="{{ $val }}" @checked(old('course_type', array_key_first($courseTypes)) === $val) required>
                            <span class="seg-btn">{{ $name }}<span class="seg-sub">{{ $sub }}</span></span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ② 목표 기록 --}}
        <div class="card">
            <div class="card-label">② 목표 완주기록</div>
            <div class="field">
                <label class="fl">목표기록 <span class="req">*</span> <span class="hint">H:MM:SS</span></label>
                <div class="time-row">
                    <div>
                        <input type="number" name="goal_h" class="time-inp" placeholder="3" min="0" max="9" value="{{ old('goal_h', 3) }}" required>
                        <div class="time-label">시간</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" name="goal_m" class="time-inp" placeholder="30" min="0" max="59" value="{{ old('goal_m', 30) }}" required>
                        <div class="time-label">분</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" name="goal_s" class="time-inp" placeholder="00" min="0" max="59" value="{{ old('goal_s', 0) }}" required>
                        <div class="time-label">초</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ③ 훈련 상태 --}}
        <div class="card">
            <div class="card-label">③ 훈련 상태</div>
            <div class="field">
                <div class="radio-grid">
                    @foreach(['best'=>['최상','피크 훈련 완료'], 'good'=>['좋음','꾸준히 훈련 중'], 'normal'=>['보통','기본 훈련 완료'], 'poor'=>['부족','훈련 부족']] as $val=>[$title,$desc])
                        <label class="radio-card">
                            <input type="radio" name="training_status" value="{{ $val }}" @checked(old('training_status', 'normal') === $val)>
                            <span class="radio-body">
                                <span class="radio-title">{{ $title }}</span>
                                <span class="radio-desc">{{ $desc }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ④ 추가 정보 (선택) --}}
        <div class="card">
            <div class="card-label">④ 추가 정보 <span style="font-size:0.65rem;font-weight:400;color:var(--mut);letter-spacing:0;">(선택 — 입력 시 플랜 정밀도 향상)</span></div>
            <div class="field">
                <label class="fl">최근 최장 거리 훈련 <span class="hint">최근 1개월 기준 (km)</span></label>
                <input type="number" name="recent_long_km" class="fi" placeholder="예: 32" min="1" max="200" step="0.1" value="{{ old('recent_long_km') }}">
            </div>
            <div class="field">
                <label class="fl">최근 10km 기록 <span class="hint">H:MM:SS</span></label>
                <div class="time-row">
                    <div>
                        <input type="number" name="recent_10k_h" class="time-inp" placeholder="0" min="0" max="2" value="{{ old('recent_10k_h', 0) }}">
                        <div class="time-label">시간</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" name="recent_10k_m" class="time-inp" placeholder="50" min="0" max="59" value="{{ old('recent_10k_m') }}">
                        <div class="time-label">분</div>
                    </div>
                    <span class="time-sep">:</span>
                    <div>
                        <input type="number" name="recent_10k_s" class="time-inp" placeholder="00" min="0" max="59" value="{{ old('recent_10k_s', 0) }}">
                        <div class="time-label">초</div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submit-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            AI 레이스 플랜 생성하기
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('plan-form').addEventListener('submit', function () {
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('loading').classList.add('active');
});
</script>
@endpush
