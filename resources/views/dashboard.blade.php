<x-app-layout>

<style>
    .dash-wrap { max-width: 1100px; margin: 0 auto; padding: 2.75rem 1.5rem 5rem; }

    .dash-welcome {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .dash-eyebrow {
        font-size: 0.68rem; font-weight: 700; letter-spacing: 0.22em;
        text-transform: uppercase; color: var(--accent); margin-bottom: 0.4rem;
    }
    .dash-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(2rem, 4vw, 2.8rem);
        letter-spacing: 0.04em; line-height: 1;
        color: var(--text);
    }
    .dash-sub { font-size: 0.82rem; color: var(--muted); margin-top: 0.45rem; }

    .dash-cta {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1.1rem;
        background: var(--accent); color: #fff;
        border-radius: 8px; font-size: 0.82rem; font-weight: 600;
        transition: background 0.15s; white-space: nowrap;
    }
    .dash-cta:hover { background: var(--accent-d); }

    /* ── ACTION CARDS ─────────────────────────────── */
    .action-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2.5rem; }
    .action-card {
        display: block;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 12px; padding: 1.4rem;
        transition: border-color 0.2s, transform 0.15s;
        position: relative; overflow: hidden;
    }
    .action-card::before {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: var(--accent); opacity: 0;
        transition: opacity 0.2s;
    }
    .action-card:hover { border-color: rgba(255,107,53,0.25); transform: translateY(-1px); }
    .action-card:hover::before { opacity: 1; }
    .action-card-icon { font-size: 1.5rem; margin-bottom: 0.85rem; }
    .action-card-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.05rem; letter-spacing: 0.08em;
        color: var(--text); margin-bottom: 0.3rem;
    }
    .action-card-desc { font-size: 0.74rem; color: var(--muted); line-height: 1.55; }

    /* ── INFO CARD ────────────────────────────────── */
    .info-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; }
    .info-panel-head {
        font-size: 0.65rem; font-weight: 700; letter-spacing: 0.16em;
        text-transform: uppercase; color: var(--muted);
        margin-bottom: 1.25rem; padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 2rem; }
    .info-label { font-size: 0.7rem; color: var(--muted); margin-bottom: 0.3rem; letter-spacing: 0.04em; }
    .info-value { font-size: 0.9rem; color: var(--text2); font-weight: 500; }

    @media (max-width: 560px) {
        .dash-wrap { padding: 1.75rem 1rem 3rem; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="dash-wrap">

    {{-- Welcome --}}
    <div class="dash-welcome">
        <div>
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
        <a href="{{ route('races.index', ['status' => '접수중']) }}" class="action-card">
            <div class="action-card-icon">📋</div>
            <div class="action-card-title">접수 중인 대회</div>
            <div class="action-card-desc">지금 신청 가능한 대회 목록</div>
        </a>
        <a href="{{ route('profile.edit') }}" class="action-card">
            <div class="action-card-icon">👤</div>
            <div class="action-card-title">내 프로필</div>
            <div class="action-card-desc">계정 정보 및 비밀번호 관리</div>
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
                <div class="info-value" style="color: {{ auth()->user()->email_verified_at ? '#4ADE80' : '#F87171' }}">
                    {{ auth()->user()->email_verified_at ? '인증 완료' : '인증 필요' }}
                </div>
            </div>
        </div>
    </div>

</div>

</x-app-layout>
