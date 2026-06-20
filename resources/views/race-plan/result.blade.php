@extends('layouts.review')

@section('title', '레이스 플랜 — ' . ($plan['race_name'] ?? $race->name) . ' — PAC-RUN')

@push('styles')
<style>
    :root { --bg:#0A0A0A; --s:#fff; --s2:#F7F8FA; --bd:#E8EAEE; --acc:#E80043; --acc-d:#C20038; --text:#16181D; --t2:#5A6170; --mut:#9AA1AE; --green:#16A34A; --blue:#2563EB; }

    /* ── HERO ── */
    .plan-hero { border-bottom: 1px solid var(--bd); padding: 2.5rem 1.5rem 2rem; background: radial-gradient(ellipse 60% 50% at 0% 0%, rgba(255,107,53,0.08), transparent 65%), var(--bg); }
    .plan-hero-inner { max-width: 860px; margin: 0 auto; }
    .back-link { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--mut); margin-bottom: 1.5rem; transition: color 0.15s; }
    .back-link:hover { color: var(--t2); }
    .back-link svg { transition: transform 0.15s; }
    .back-link:hover svg { transform: translateX(-2px); }
    .hero-eyebrow { font-family: 'Archivo', sans-serif; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: var(--acc); margin-bottom: 0.35rem; }
    .hero-race { font-family: 'Bebas Neue', 'Archivo', sans-serif; font-size: clamp(2rem,5vw,3.5rem); letter-spacing: 0.04em; color: var(--text); line-height: 1; margin-bottom: 0.75rem; }
    .hero-meta { display: flex; flex-wrap: wrap; gap: 0.5rem 1.5rem; font-size: 0.82rem; color: var(--t2); }
    .hero-meta-item { display: flex; align-items: center; gap: 0.4rem; }
    .goal-badge { font-family: 'Archivo', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--acc); letter-spacing: 0.04em; }

    /* ── PAGE LAYOUT ── */
    .page-wrap { max-width: 860px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }

    /* ── SECTION ── */
    .section { margin-bottom: 1.5rem; }
    .section-title { font-family: 'Archivo', sans-serif; font-size: 0.62rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: var(--mut); margin-bottom: 1rem; padding-bottom: 0.65rem; border-bottom: 1px solid var(--bd); }
    .card { background: var(--s); border: 1px solid var(--bd); border-radius: 12px; padding: 1.4rem 1.5rem; box-shadow: 0 1px 3px rgba(22,24,29,0.05); }

    /* ── WEATHER ── */
    .weather-grid { display: flex; flex-wrap: wrap; gap: 0.6rem; }
    .weather-chip { background: var(--s2); border: 1px solid var(--bd); border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.8rem; color: var(--t2); }
    .weather-assessment { font-size: 0.88rem; color: var(--t2); line-height: 1.7; margin-top: 0.85rem; }

    /* ── STRATEGY ── */
    .strategy-text { font-size: 0.9rem; color: var(--t2); line-height: 1.8; }

    /* ── PACE TABLE ── */
    .pace-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .pace-table thead tr { border-bottom: 1px solid var(--bd); }
    .pace-table th { text-align: left; padding: 0.4rem 0.6rem; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--mut); }
    .pace-table td { padding: 0.65rem 0.6rem; border-bottom: 1px solid rgba(36,36,36,0.5); color: var(--t2); vertical-align: top; }
    .pace-table tbody tr:last-child td { border-bottom: none; }
    .pace-val { font-family: 'Archivo', sans-serif; font-size: 0.95rem; font-weight: 700; color: var(--acc); }
    .cumul-val { font-family: 'Archivo', sans-serif; font-size: 0.82rem; color: var(--t2); }
    .seg-note { font-size: 0.78rem; color: var(--mut); line-height: 1.5; }

    /* ── ATTENTION SEGMENTS ── */
    .attn-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .attn-item { background: rgba(248,113,113,0.04); border: 1px solid rgba(248,113,113,0.15); border-radius: 8px; padding: 1rem 1.1rem; }
    .attn-seg { font-family: 'Archivo', sans-serif; font-size: 0.8rem; font-weight: 700; color: #F87171; margin-bottom: 0.3rem; }
    .attn-reason { font-size: 0.82rem; color: var(--t2); margin-bottom: 0.3rem; }
    .attn-tip { font-size: 0.78rem; color: var(--mut); }
    .attn-tip::before { content: '💡 '; }

    /* ── NUTRITION ── */
    .nutrition-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .nutrition-item { }
    .nutrition-key { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; color: var(--mut); text-transform: uppercase; margin-bottom: 0.4rem; font-family: 'Archivo', sans-serif; }
    .nutrition-val { font-size: 0.82rem; color: var(--t2); line-height: 1.6; }
    .gel-chips { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.35rem; }
    .gel-chip { font-family: 'Archivo', sans-serif; font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.2); color: var(--blue); border-radius: 4px; }

    /* ── HEART RATE ── */
    .hr-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 0.85rem; }
    .hr-item { background: var(--s2); border-radius: 8px; padding: 0.75rem; text-align: center; }
    .hr-label { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.08em; color: var(--mut); text-transform: uppercase; font-family: 'Archivo', sans-serif; margin-bottom: 0.35rem; }
    .hr-val { font-family: 'Archivo', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--green); }
    .hr-advice { font-size: 0.82rem; color: var(--mut); line-height: 1.65; }

    /* ── RAG BASIS ── */
    .rag-badge { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--t2); background: var(--s2); border: 1px solid var(--bd); border-radius: 6px; padding: 0.35rem 0.75rem; margin-bottom: 0.75rem; }
    .rag-summary { font-size: 0.82rem; color: var(--mut); line-height: 1.7; }

    /* ── ACTIONS ── */
    .action-row { display: flex; gap: 0.75rem; margin-top: 1.5rem; flex-wrap: wrap; }
    .btn-again { padding: 0.65rem 1.4rem; background: transparent; border: 1.5px solid var(--acc); color: var(--acc); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: all 0.15s; }
    .btn-again:hover { background: rgba(232,0,67,0.06); }
    .btn-race { padding: 0.65rem 1.4rem; background: var(--acc); color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; font-family: 'Pretendard', sans-serif; transition: background 0.15s; }
    .btn-race:hover { background: var(--acc-d); }

    @media (max-width: 620px) { .nutrition-grid { grid-template-columns: 1fr; } .hr-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')
<section class="plan-hero">
    <div class="plan-hero-inner">
        <a href="{{ route('races.show', $race) }}" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
            {{ $race->name }}
        </a>
        <p class="hero-eyebrow">AI Race Plan</p>
        <h1 class="hero-race">{{ $plan['race_name'] ?? $race->name }}</h1>
        <div class="hero-meta">
            <span class="hero-meta-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                목표: <span class="goal-badge">{{ $plan['goal_time'] ?? '' }}</span>
            </span>
            @if($plan['_meta']['course_type'] ?? false)
                <span class="hero-meta-item">{{ ['FULL'=>'풀마라톤','HALF'=>'하프마라톤','10K'=>'10K'][$plan['_meta']['course_type']] ?? '' }}</span>
            @endif
            @if($edition->year)
                <span class="hero-meta-item">{{ $edition->year }}년</span>
            @endif
        </div>
    </div>
</section>

<div class="page-wrap">

    {{-- 날씨 --}}
    @if(!empty($plan['weather_summary']) || !empty($plan['weather_assessment']))
    <div class="section">
        <div class="card">
            <div class="section-title">대회일 날씨 조건</div>
            @if(!empty($plan['weather_summary']))
                <div class="weather-chip" style="display:inline-block;margin-bottom:0.65rem;">🌤 {{ $plan['weather_summary'] }}</div>
            @endif
            @if(!empty($plan['weather_assessment']))
                <div class="weather-assessment">{{ $plan['weather_assessment'] }}</div>
            @endif
        </div>
    </div>
    @endif

    {{-- 전략 개요 --}}
    @if(!empty($plan['strategy_overview']))
    <div class="section">
        <div class="card">
            <div class="section-title">전체 레이스 전략</div>
            <p class="strategy-text">{{ $plan['strategy_overview'] }}</p>
        </div>
    </div>
    @endif

    {{-- 구간별 목표 페이스 --}}
    @if(!empty($plan['pace_plan']))
    <div class="section">
        <div class="card">
            <div class="section-title">구간별 목표 페이스</div>
            <table class="pace-table">
                <thead>
                    <tr>
                        <th>구간</th>
                        <th>목표 페이스</th>
                        <th>누적 기록</th>
                        <th>전략 포인트</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan['pace_plan'] as $seg)
                        <tr>
                            <td><span style="font-weight:600;color:var(--text)">{{ $seg['segment'] ?? '-' }}</span></td>
                            <td><span class="pace-val">{{ $seg['target_pace'] ?? '-' }}</span></td>
                            <td><span class="cumul-val">{{ $seg['cumulative_time'] ?? '-' }}</span></td>
                            <td><span class="seg-note">{{ $seg['note'] ?? '' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- 주의 구간 --}}
    @if(!empty($plan['attention_segments']))
    <div class="section">
        <div class="card">
            <div class="section-title">⚠ 주의 구간</div>
            <div class="attn-list">
                @foreach($plan['attention_segments'] as $attn)
                    <div class="attn-item">
                        <div class="attn-seg">{{ $attn['segment'] ?? '' }}</div>
                        <div class="attn-reason">{{ $attn['reason'] ?? '' }}</div>
                        @if(!empty($attn['tip']))
                            <div class="attn-tip">{{ $attn['tip'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- 영양 전략 --}}
    @if(!empty($plan['nutrition_plan']))
    <div class="section">
        <div class="card">
            <div class="section-title">영양 &amp; 수분 전략</div>
            @php $nut = $plan['nutrition_plan']; @endphp
            <div class="nutrition-grid">
                <div class="nutrition-item">
                    <div class="nutrition-key">수분 전략</div>
                    <div class="nutrition-val">{{ $nut['hydration'] ?? '-' }}</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-key">에너지젤 타이밍</div>
                    @if(!empty($nut['gel_timing']))
                        <div class="gel-chips">
                            @foreach((array)$nut['gel_timing'] as $km)
                                <span class="gel-chip">{{ $km }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="nutrition-val">-</div>
                    @endif
                </div>
            </div>
            @if(!empty($nut['advice']))
                <div style="margin-top:0.85rem;padding-top:0.75rem;border-top:1px solid var(--bd);">
                    <div class="nutrition-val">{{ $nut['advice'] }}</div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- 심박 관리 --}}
    @if(!empty($plan['heart_rate_guide']))
    <div class="section">
        <div class="card">
            <div class="section-title">심박 관리 가이드</div>
            @php $hr = $plan['heart_rate_guide']; @endphp
            <div class="hr-grid">
                <div class="hr-item">
                    <div class="hr-label">초반</div>
                    <div class="hr-val">{{ $hr['early_phase_bpm'] ?? '-' }}</div>
                </div>
                <div class="hr-item">
                    <div class="hr-label">중반</div>
                    <div class="hr-val">{{ $hr['mid_phase_bpm'] ?? '-' }}</div>
                </div>
                <div class="hr-item">
                    <div class="hr-label">후반</div>
                    <div class="hr-val">{{ $hr['late_phase_bpm'] ?? '-' }}</div>
                </div>
            </div>
            @if(!empty($hr['advice']))
                <div class="hr-advice">{{ $hr['advice'] }}</div>
            @endif
        </div>
    </div>
    @endif

    {{-- RAG 근거 --}}
    @if(!empty($plan['rag_basis']))
    <div class="section">
        <div class="card">
            <div class="section-title">데이터 기반 분석</div>
            @php $rag = $plan['rag_basis']; @endphp
            <div class="rag-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3S3 13.66 3 12"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                유사 완주 케이스 {{ $rag['cases_found'] ?? 0 }}건 참조
            </div>
            @if(!empty($rag['summary']))
                <div class="rag-summary">{{ $rag['summary'] }}</div>
            @endif
        </div>
    </div>
    @endif

    <div class="action-row">
        <a href="{{ route('race-plan.create', $race) }}" class="btn-again">다시 생성하기</a>
        <a href="{{ route('races.show', $race) }}" class="btn-race">대회 상세로</a>
    </div>

</div>
@endsection
