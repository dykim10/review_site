<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PAC-RUN') }}</title>

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
        html { scroll-behavior: smooth; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Noto Sans KR', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        /* ── HEADER ──────────────────────────────────── */
        .h-bar {
            position: sticky; top: 0; z-index: 200;
            height: 54px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem;
            background: rgba(10,10,10,0.92);
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .h-logo { font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; letter-spacing: 0.12em; color: var(--accent); }
        .h-logo span { color: var(--text); }
        .h-nav { display: flex; align-items: center; gap: 1.25rem; }
        .h-link { font-size: 0.78rem; font-weight: 500; color: var(--muted); letter-spacing: 0.04em; transition: color 0.15s; }
        .h-link:hover { color: var(--text); }
        .h-link-active { color: var(--text) !important; }
        .h-link-admin { color: var(--accent); }
        .h-link-admin:hover { color: var(--accent-d); }
        .h-btn { padding: 0.35rem 0.9rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; font-family: 'Noto Sans KR', sans-serif; transition: all 0.15s; }
        .h-btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text2); }
        .h-btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .h-btn-fill { background: var(--accent); color: #fff; }
        .h-btn-fill:hover { background: var(--accent-d); }
        .h-btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .h-btn-ghost:hover { color: var(--text); border-color: var(--text2); }

        /* ── PAGE HEADER SLOT ────────────────────────── */
        .page-header-bar {
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            padding: 1.25rem 1.5rem;
        }
        .page-header-bar h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem; letter-spacing: 0.06em;
            color: var(--text);
            max-width: 1100px; margin: 0 auto;
        }

        /* ── LARAVEL PAGINATION OVERRIDE ────────────── */
        nav[role="navigation"] { display: flex; justify-content: center; padding: 1rem 0; }
        nav[role="navigation"] > div:first-child { display: none; }
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] a {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 32px; height: 32px; padding: 0 0.5rem;
            border-radius: 5px; font-size: 0.78rem;
            border: 1px solid var(--border);
            color: var(--muted); background: var(--surface);
            transition: all 0.15s;
        }
        nav[role="navigation"] a:hover { border-color: var(--accent); color: var(--accent); }
        nav[role="navigation"] span[aria-current="page"] > span { background: var(--accent); border-color: var(--accent); color: #fff; }

        @media (max-width: 480px) {
            .h-nav .h-link { display: none; }
        }
    </style>
</head>
<body>
    @include('layouts.navigation')

    @isset($header)
        <div class="page-header-bar">
            {{ $header }}
        </div>
    @endisset

    <main>
        {{ $slot }}
    </main>
</body>
</html>
