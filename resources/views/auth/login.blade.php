<x-guest-layout>
    <style>
        .auth-heading { margin-bottom: 1.75rem; }
        .auth-heading h1 { font-family: 'Bebas Neue', sans-serif; font-size: 1.8rem; letter-spacing: 0.06em; color: var(--text); margin-bottom: 0.25rem; }
        .auth-heading p { font-size: 0.78rem; color: var(--muted); }

        .auth-field { margin-bottom: 1.2rem; }
        .auth-label { display: block; font-size: 0.73rem; font-weight: 600; letter-spacing: 0.06em; color: var(--text2); margin-bottom: 0.45rem; }
        .auth-input { display: block; width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 7px; padding: 0.6rem 0.85rem; font-size: 0.88rem; color: var(--text); font-family: 'Noto Sans KR', sans-serif; outline: none; transition: border-color 0.2s; }
        .auth-input:focus { border-color: var(--accent); }
        .auth-input::placeholder { color: var(--muted); }
        .auth-input.error { border-color: rgba(248,113,113,0.45); }
        .auth-field-error { font-size: 0.72rem; color: #F87171; margin-top: 0.35rem; }

        .auth-check-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
        .auth-check-row input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--accent); cursor: pointer; }
        .auth-check-row label { font-size: 0.78rem; color: var(--muted); cursor: pointer; }

        .auth-divider { border: none; border-top: 1px solid var(--border); margin: 1.5rem 0; }

        .auth-actions { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .auth-submit { padding: 0.6rem 1.5rem; background: var(--accent); color: #fff; border: none; border-radius: 7px; font-size: 0.85rem; font-weight: 600; font-family: 'Noto Sans KR', sans-serif; cursor: pointer; transition: background 0.15s; }
        .auth-submit:hover { background: var(--accent-d); }
        .auth-link { font-size: 0.78rem; color: var(--muted); transition: color 0.15s; }
        .auth-link:hover { color: var(--accent); }

        .auth-footer { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border); text-align: center; }
        .auth-footer p { font-size: 0.78rem; color: var(--muted); }
        .auth-footer a { color: var(--accent); transition: color 0.15s; }
        .auth-footer a:hover { color: var(--accent-d); }

        .status-msg { padding: 0.65rem 0.9rem; background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.2); border-radius: 7px; font-size: 0.8rem; color: #4ADE80; margin-bottom: 1.25rem; }
    </style>

    @if(session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <div class="auth-heading">
        <h1>로그인</h1>
        <p>PAC-RUN에 오신 것을 환영합니다</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">이메일</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   placeholder="your@email.com" required autofocus autocomplete="username">
            @error('email')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">비밀번호</label>
            <input id="password" type="password" name="password"
                   class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                   placeholder="••••••••" required autocomplete="current-password">
            @error('password')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-check-row">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">로그인 상태 유지</label>
        </div>

        <div class="auth-actions">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">비밀번호를 잊으셨나요?</a>
            @endif
            <button type="submit" class="auth-submit">로그인</button>
        </div>
    </form>

    @if(Route::has('register'))
        <div class="auth-footer">
            <p>아직 계정이 없으신가요? <a href="{{ route('register') }}">회원가입</a></p>
        </div>
    @endif
</x-guest-layout>
