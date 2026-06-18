<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '관리자 — PAC-RUN')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            background: #F1F2F5; color: #1A1D24; font-family: 'Pretendard', -apple-system, sans-serif;
            font-size: 14px; -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        .adm-shell { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .adm-side { width: 220px; flex-shrink: 0; background: #1A1D24; color: #9CA3AF; display: flex; flex-direction: column; }
        .adm-logo { padding: 1.25rem 1.25rem 1rem; font-family: 'Archivo', sans-serif; font-weight: 700; font-size: 1.1rem; color: #fff; letter-spacing: -0.01em; }
        .adm-logo span { color: #E80043; }
        .adm-logo-sub { font-size: 0.65rem; color: #6B7280; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 0.15rem; }

        .adm-nav { flex: 1; padding: 0.5rem 0.75rem; }
        .adm-nav-group { margin-bottom: 1.5rem; }
        .adm-nav-label { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #565D6B; padding: 0 0.6rem; margin-bottom: 0.5rem; }
        .adm-nav-item {
            display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 0.6rem; border-radius: 7px;
            font-size: 0.84rem; font-weight: 500; color: #B0B5BE; border-left: 2px solid transparent; transition: all 0.15s;
        }
        .adm-nav-item:hover { background: #23262E; color: #fff; }
        .adm-nav-item-active { background: #23262E; color: #fff; border-left-color: #E80043; }
        .adm-nav-ico { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.85; }

        .adm-side-foot { padding: 1rem 0.75rem; border-top: 1px solid #2A2D36; }
        .adm-back-link { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.6rem; font-size: 0.8rem; color: #9CA3AF; border-radius: 7px; transition: all 0.15s; }
        .adm-back-link:hover { background: #23262E; color: #fff; }

        /* ── MAIN ── */
        .adm-main { flex: 1; min-width: 0; }
        .adm-topbar {
            height: 60px; background: #fff; border-bottom: 1px solid #E5E7EB; display: flex;
            align-items: center; justify-content: space-between; padding: 0 1.75rem; position: sticky; top: 0; z-index: 10;
        }
        .adm-title { font-size: 1.1rem; font-weight: 700; color: #1A1D24; }
        .adm-content { padding: 1.75rem; max-width: 1100px; }

        /* ── SHARED UI ── */
        .adm-btn {
            display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 7px;
            font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; font-family: 'Pretendard', sans-serif;
        }
        .adm-btn-primary { background: #E80043; color: #fff; }
        .adm-btn-primary:hover { background: #C20038; }
        .adm-btn-ghost { background: #fff; color: #4B5563; border: 1px solid #D1D5DB; }
        .adm-btn-ghost:hover { background: #F9FAFB; border-color: #9CA3AF; }
        .adm-btn-danger { background: transparent; color: #DC2626; padding: 0.3rem 0.5rem; font-weight: 500; }
        .adm-btn-danger:hover { text-decoration: underline; }

        .adm-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; }
        .adm-alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; border-radius: 8px; padding: 0.7rem 1rem; font-size: 0.83rem; margin-bottom: 1.25rem; }
        .adm-alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; border-radius: 8px; padding: 0.7rem 1rem; font-size: 0.83rem; margin-bottom: 1.25rem; }

        .adm-table { width: 100%; font-size: 0.84rem; border-collapse: collapse; }
        .adm-table thead th { text-align: left; padding: 0.7rem 1.1rem; background: #F9FAFB; color: #6B7280; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; border-bottom: 1px solid #E5E7EB; }
        .adm-table tbody tr { border-bottom: 1px solid #F1F2F5; transition: background 0.1s; }
        .adm-table tbody tr:hover { background: #FAFBFC; }
        .adm-table tbody tr:last-child { border-bottom: none; }
        .adm-table td { padding: 0.75rem 1.1rem; vertical-align: middle; color: #1A1D24; }
        .adm-td-muted { color: #9CA3AF; }
        .adm-td-empty { padding: 3rem; text-align: center; color: #9CA3AF; }
        .adm-link { color: #2563EB; font-weight: 500; }
        .adm-link:hover { text-decoration: underline; }

        .adm-badge { display: inline-block; padding: 0.18rem 0.55rem; border-radius: 5px; font-size: 0.72rem; font-weight: 600; }
        .adm-badge-blue { background: #DBEAFE; color: #1D4ED8; }
        .adm-badge-green { background: #DCFCE7; color: #15803D; }
        .adm-badge-gray { background: #F1F2F5; color: #565D6B; }

        .adm-form-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 1.75rem; }
        .adm-field { margin-bottom: 1.1rem; }
        .adm-label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .adm-label-hint { font-weight: 400; color: #9CA3AF; font-size: 0.72rem; }
        .adm-input {
            width: 100%; border: 1px solid #D1D5DB; border-radius: 7px; padding: 0.55rem 0.75rem; font-size: 0.85rem;
            color: #1A1D24; font-family: 'Pretendard', sans-serif; outline: none; transition: border-color 0.15s;
        }
        .adm-input:focus { border-color: #E80043; box-shadow: 0 0 0 3px rgba(232,0,67,0.08); }
        .adm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
        .adm-field-error { font-size: 0.75rem; color: #DC2626; margin-top: 0.3rem; }

        @media (max-width: 760px) {
            .adm-side { display: none; }
            .adm-content { padding: 1.1rem; }
            .adm-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="adm-shell">
        <aside class="adm-side">
            <div class="adm-logo">PAC<span>-RUN</span>
                <div class="adm-logo-sub">Admin Console</div>
            </div>

            <nav class="adm-nav">
                <div class="adm-nav-group">
                    <div class="adm-nav-label">대회 관리</div>
                    <a href="{{ route('admin.races.index') }}" class="adm-nav-item {{ request()->routeIs('admin.races.*') ? 'adm-nav-item-active' : '' }}">
                        <svg class="adm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V8l8-5 8 5v13"/><path d="M9 21V12h6v9"/></svg>
                        대회
                    </a>
                    <a href="{{ route('admin.race-editions.index') }}" class="adm-nav-item {{ request()->routeIs('admin.race-editions.*') ? 'adm-nav-item-active' : '' }}">
                        <svg class="adm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        대회 인스턴스
                    </a>
                    <a href="{{ route('admin.race-courses.index') }}" class="adm-nav-item {{ request()->routeIs('admin.race-courses.*') ? 'adm-nav-item-active' : '' }}">
                        <svg class="adm-nav-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 20c2-4 4-6 7-6s4 4 7 4 3-3 4-6"/></svg>
                        코스 GPX
                    </a>
                </div>
            </nav>

            <div class="adm-side-foot">
                <a href="{{ route('races.index') }}" class="adm-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                    공개 사이트로
                </a>
            </div>
        </aside>

        <div class="adm-main">
            <div class="adm-topbar">
                <h1 class="adm-title">@yield('page-title', '관리자')</h1>
                @hasSection('topbar-action')
                    @yield('topbar-action')
                @endif
            </div>
            <div class="adm-content">
                @if(session('success'))
                    <div class="adm-alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="adm-alert-error">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
