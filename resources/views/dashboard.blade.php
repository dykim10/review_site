@extends('layouts.review')
@section('title', '대시보드 — PAC-RUN')

@push('styles')
<style>
    .dash-wrap { max-width: 1100px; margin: 0 auto; padding: 2.75rem 1.5rem 5rem; }

    .dash-welcome {
        margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid #E8EAEE;
        display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .dash-welcome__text { flex: 1; min-width: 0; }
    .dash-eyebrow {
        font-family: 'Archivo', sans-serif; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.22em;
        text-transform: uppercase; color: #E80043; margin-bottom: 0.4rem;
    }
    .dash-title { font-size: clamp(1.6rem, 4vw, 2.2rem); font-weight: 700; letter-spacing: -0.01em; line-height: 1.15; color: #16181D; }
    .dash-sub { font-size: 0.85rem; color: #5A6170; margin-top: 0.45rem; }

    .dash-cta {
        display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.1rem;
        background: #E80043; color: #fff; border-radius: 999px; font-size: 0.82rem; font-weight: 600;
        transition: background 0.15s; white-space: nowrap;
    }
    .dash-cta:hover { background: #C20038; }

    /* ── ACTION CARDS ─────────────────────────────── */
    .action-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2.5rem; }
    .action-card {
        display: block; background: #fff; border: 1px solid #E8EAEE; border-radius: 12px; padding: 1.4rem;
        transition: border-color 0.2s, transform 0.15s, box-shadow 0.2s; position: relative; overflow: hidden;
    }
    .action-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: #E80043; opacity: 0; transition: opacity 0.2s;
    }
    .action-card:hover { border-color: rgba(232,0,67,0.3); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(22,24,29,0.06); }
    .action-card:hover::before { opacity: 1; }
    .action-card-icon { font-size: 1.5rem; margin-bottom: 0.85rem; }
    .action-card-title { font-size: 1rem; font-weight: 700; color: #16181D; margin-bottom: 0.3rem; }
    .action-card-desc { font-size: 0.78rem; color: #5A6170; line-height: 1.55; }

    /* ── INFO CARD ────────────────────────────────── */
    .info-panel { background: #fff; border: 1px solid #E8EAEE; border-radius: 12px; padding: 1.5rem; }
    .info-panel-head {
        font-family: 'Archivo', sans-serif; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.16em;
        text-transform: uppercase; color: #9AA1AE; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E8EAEE;
    }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 2rem; }
    .info-label { font-size: 0.7rem; color: #9AA1AE; margin-bottom: 0.3rem; letter-spacing: 0.04em; }
    .info-value { font-size: 0.9rem; color: #16181D; font-weight: 500; }

    @media (max-width: 560px) {
        .dash-wrap { padding: 1.75rem 1rem 3rem; }
        .dash-welcome { flex-direction: column; align-items: stretch; }
        .dash-cta { align-self: flex-start; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dash-wrap">

    {{-- Welcome --}}
    <div class="dash-welcome">
        <div class="dash-welcome__text">
            <p class="dash-eyebrow">Dashboard</p>
            <h1 class="dash-title">안녕하세요, {{ auth()->user()->name }}</h1>
            <p class="dash-sub">오늘도 달려볼까요? 새로운 대회를 찾아보세요.</p>
        </div>
        <a href="{{ route('races.index') }}" class="dash-cta">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            대회 찾기
        </a>
    </div>

    {{-- Quick Actions --}}
    <div class="action-grid">
        <a href="{{ route('races.index') }}" class="action-card">
            <div class="action-card-icon">🏃</div>
            <div class="action-card-title">대회 검색</div>
            <div class="action-card-desc">마라톤·러닝 대회를 찾아보세요</div>
        </a>
        <a href="{{ route('races.index') }}#upcoming" class="action-card">
            <div class="action-card-icon">📋</div>
            <div class="action-card-title">다가오는 대회</div>
            <div class="action-card-desc">곧 개최되는 대회 일정 보기</div>
        </a>
        <a href="{{ route('profile.edit') }}" class="action-card">
            <div class="action-card-icon">👤</div>
            <div class="action-card-title">내 프로필</div>
            <div class="action-card-desc">계정 정보 관리</div>
        </a>
    </div>

    {{-- Account Info --}}
    <div class="info-panel">
        <div class="info-panel-head">계정 정보</div>
        <div class="info-grid">
            <div>
                <div class="info-label">이름</div>
                <div class="info-value">{{ auth()->user()->name }}</div>
            </div>
            <div>
                <div class="info-label">이메일</div>
                <div class="info-value">{{ auth()->user()->email }}</div>
            </div>
            <div>
                <div class="info-label">가입일</div>
                <div class="info-value">{{ auth()->user()->created_at->format('Y년 m월 d일') }}</div>
            </div>
            <div>
                <div class="info-label">이메일 인증</div>
                <div class="info-value" style="color: {{ auth()->user()->email_verified_at ? '#15803D' : '#DC2626' }}">
                    {{ auth()->user()->email_verified_at ? '인증 완료' : '인증 필요' }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
