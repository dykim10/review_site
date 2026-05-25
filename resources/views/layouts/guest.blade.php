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
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Noto Sans KR', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            /* Subtle grain overlay */
            background-image:
                radial-gradient(ellipse 60% 50% at 50% 0%, rgba(255,107,53,0.05), transparent 70%);
        }
        a { text-decoration: none; color: inherit; }

        /* ── LOGO ────────────────────────────────────── */
        .auth-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            letter-spacing: 0.12em;
            color: var(--accent);
            margin-bottom: 2rem;
        }
        .auth-logo span { color: var(--text); }

        /* ── AUTH CARD ───────────────────────────────── */
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 2rem 2rem;
            box-shadow: 0 24px 48px rgba(0,0,0,0.4);
        }

        /* ── FORM ELEMENTS OVERRIDE ──────────────────── */
        .auth-card label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: var(--text2);
            margin-bottom: 0.45rem;
        }
        .auth-card input[type="email"],
        .auth-card input[type="password"],
        .auth-card input[type="text"] {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 0.6rem 0.85rem;
            font-size: 0.88rem;
            color: var(--text);
            font-family: 'Noto Sans KR', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }
        .auth-card input:focus { border-color: var(--accent); }
        .auth-card input::placeholder { color: var(--muted); }
        .auth-card input.border-red-300,
        .auth-card input.border-red-500 { border-color: rgba(248,113,113,0.5) !important; }

        /* Checkbox */
        .auth-card input[type="checkbox"] {
            width: auto;
            accent-color: var(--accent);
        }

        /* Primary Button override */
        .auth-card button[type="submit"],
        .auth-card .auth-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.4rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Noto Sans KR', sans-serif;
            cursor: pointer;
            transition: background 0.15s;
        }
        .auth-card button[type="submit"]:hover,
        .auth-card .auth-submit:hover { background: var(--accent-d); }

        /* Error messages */
        .auth-card .text-red-600,
        .auth-card .text-red-500 { color: #F87171 !important; font-size: 0.75rem; margin-top: 0.35rem; }

        /* Links */
        .auth-card a { color: var(--text2); font-size: 0.78rem; transition: color 0.15s; }
        .auth-card a:hover { color: var(--accent); }

        /* Status message */
        .auth-status {
            padding: 0.65rem 0.9rem;
            background: rgba(74,222,128,0.08);
            border: 1px solid rgba(74,222,128,0.2);
            border-radius: 7px;
            font-size: 0.8rem;
            color: #4ADE80;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="auth-logo">PAC<span>-RUN</span></a>

    <div class="auth-card">
        {{ $slot }}
    </div>
</body>
</html>
