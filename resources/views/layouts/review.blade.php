<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'PAC-RUN') . ' — 마라톤 대회 리뷰')</title>

    {{-- Pretendard (CDN) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background-color: #F7F8FA;
            color: #16181D;
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            font-size: 16px;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a { text-decoration: none; color: inherit; }

        /* 페이지네이션 오버라이드 */
        nav[role="navigation"] { display: flex; justify-content: center; padding: 1.5rem 0; }
        nav[role="navigation"] > div:first-child { display: none; }
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] a {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 0.5rem;
            border-radius: 6px; font-size: 0.85rem;
            border: 1px solid #E8EAEE;
            color: #5A6170; background: #fff;
            transition: all 0.15s; margin: 0 2px;
        }
        nav[role="navigation"] a:hover { border-color: #E80043; color: #E80043; }
        nav[role="navigation"] span[aria-current="page"] > span {
            background: #E80043; border-color: #E80043; color: #fff;
        }

        /* ── REVIEW shell (Tailwind/Vite 없이도 동작) ── */
        .font-archivo { font-family: 'Archivo', sans-serif; }

        .rv-header {
            position: sticky; top: 0; z-index: 50;
            background: #fff; border-bottom: 1px solid #E8EAEE;
        }
        .rv-header__inner {
            max-width: 72rem; margin: 0 auto; padding: 0 1rem;
            height: 3.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        @media (min-width: 640px) { .rv-header__inner { padding: 0 1.5rem; } }

        .rv-logo {
            font-family: 'Archivo', sans-serif; font-weight: 700; font-size: 1.15rem;
            letter-spacing: -0.02em; color: #E80043; flex-shrink: 0;
        }
        .rv-logo span { color: #16181D; }

        .rv-nav--desktop {
            display: none; align-items: center; gap: 1.75rem; flex: 1; justify-content: center;
        }
        @media (min-width: 768px) { .rv-nav--desktop { display: flex; } }

        .rv-nav-link {
            font-family: 'Archivo', sans-serif; font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase; color: #5A6170;
            padding-bottom: 0.15rem; border-bottom: 2px solid transparent; transition: color 0.15s, border-color 0.15s;
        }
        .rv-nav-link:hover { color: #16181D; }
        .rv-nav-link.is-active { color: #16181D; border-bottom-color: #E80043; }
        .rv-nav-link--admin { color: #E80043; }
        .rv-nav-link--admin:hover { color: #C20038; }

        .rv-header__actions--desktop {
            display: none; align-items: center; gap: 0.5rem; flex-shrink: 0;
        }
        @media (min-width: 768px) { .rv-header__actions--desktop { display: flex; } }

        .rv-user-name { font-size: 0.875rem; color: #5A6170; margin-right: 0.25rem; }
        .rv-user-name:hover { color: #16181D; }

        .rv-btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.35rem 1rem; font-size: 0.82rem; font-weight: 600; border-radius: 999px;
            border: 2px solid transparent; cursor: pointer; font-family: inherit;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }
        .rv-btn--ghost { border-color: #E8EAEE; color: #5A6170; background: #fff; }
        .rv-btn--ghost:hover { border-color: #5A6170; color: #16181D; }
        .rv-btn--outline { border-color: #E80043; color: #E80043; background: #fff; }
        .rv-btn--outline:hover { background: #E80043; color: #fff; }
        .rv-btn--primary { background: #E80043; color: #fff; box-shadow: 0 1px 3px rgba(232,0,67,0.25); }
        .rv-btn--primary:hover { background: #C20038; }

        .rv-mobile-menu { display: block; }
        @media (min-width: 768px) { .rv-mobile-menu { display: none; } }
        .rv-mobile-menu > summary {
            list-style: none; cursor: pointer; padding: 0.35rem; color: #5A6170;
            display: flex; align-items: center; justify-content: center;
        }
        .rv-mobile-menu > summary::-webkit-details-marker { display: none; }
        .rv-mobile-menu[open] > summary { color: #16181D; }

        .rv-mobile-panel {
            position: absolute; left: 0; right: 0; top: 100%;
            background: #fff; border-bottom: 1px solid #E8EAEE;
            padding: 0.75rem 1rem 1rem; box-shadow: 0 8px 24px rgba(22,24,29,0.06);
        }
        .rv-header__inner { position: relative; }

        .rv-mobile-panel .rv-nav-link {
            display: block; font-size: 0.875rem; letter-spacing: 0; text-transform: none;
            font-weight: 500; padding: 0.65rem 0; border-bottom: 1px solid rgba(232,234,238,0.7);
            border-left: none; border-right: none; border-top: none;
        }
        .rv-mobile-panel .rv-nav-link.is-active { border-bottom-color: rgba(232,234,238,0.7); color: #E80043; }
        .rv-mobile-panel .rv-btn { width: 100%; margin-top: 0.5rem; }
        .rv-mobile-panel__auth { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .rv-mobile-panel__auth .rv-btn { flex: 1; margin-top: 0; }

        .rv-footer {
            margin-top: 4rem; background: #fff; border-top: 1px solid #E8EAEE;
        }
        .rv-footer__inner {
            max-width: 72rem; margin: 0 auto; padding: 2.5rem 1rem;
            display: flex; flex-direction: column; gap: 1.5rem;
        }
        @media (min-width: 640px) { .rv-footer__inner { padding: 2.5rem 1.5rem; } }
        @media (min-width: 768px) {
            .rv-footer__inner { flex-direction: row; align-items: flex-start; justify-content: space-between; }
        }
        .rv-footer__logo {
            font-family: 'Archivo', sans-serif; font-weight: 700; font-size: 1.05rem;
            letter-spacing: -0.02em; color: #16181D; margin-bottom: 0.35rem;
        }
        .rv-footer__logo em { font-style: normal; color: #E80043; }
        .rv-footer__desc { font-size: 0.82rem; line-height: 1.65; color: #5A6170; max-width: 18rem; }
        .rv-footer__nav { display: flex; flex-wrap: wrap; gap: 0.5rem 1.5rem; }
        .rv-footer__nav a { font-size: 0.82rem; color: #5A6170; transition: color 0.15s; }
        .rv-footer__nav a:hover { color: #E80043; }
        .rv-footer__copy {
            max-width: 72rem; margin: 0 auto; padding: 0 1rem 1.75rem;
            font-size: 0.72rem; color: #9AA1AE; border-top: 1px solid #E8EAEE;
            padding-top: 1.25rem;
        }
        @media (min-width: 640px) { .rv-footer__copy { padding-left: 1.5rem; padding-right: 1.5rem; } }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    @include('layouts.review-nav')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('layouts.review-footer')

    @stack('scripts')
</body>
</html>
