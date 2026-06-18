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
