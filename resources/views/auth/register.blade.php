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
        .auth-field-hint { font-size: 0.7rem; color: var(--muted); margin-top: 0.3rem; }

        .auth-actions { display: flex; justify-content: flex-end; margin-top: 1.75rem; }
        .auth-submit { padding: 0.6rem 1.5rem; background: var(--accent); color: #fff; border: none; border-radius: 7px; font-size: 0.85rem; font-weight: 600; font-family: 'Noto Sans KR', sans-serif; cursor: pointer; transition: background 0.15s; }
        .auth-submit:hover { background: var(--accent-d); }

        .auth-footer { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border); text-align: center; }
        .auth-footer p { font-size: 0.78rem; color: var(--muted); }
        .auth-footer a { color: var(--accent); transition: color 0.15s; }
        .auth-footer a:hover { color: var(--accent-d); }
    </style>

    <div class="auth-heading">
        <h1>회원가입</h1>
        <p>PAC-RUN 계정을 만들어보세요</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="name">이름</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="auth-input {{ $errors->has('name') ? 'error' : '' }}"
                   placeholder="홍길동" required autofocus autocomplete="name">
            @error('name')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="email">이메일</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   placeholder="your@email.com" required autocomplete="username">
            <p class="auth-field-hint">이메일 인증이 필요합니다.</p>
            @error('email')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">비밀번호</label>
            <input id="password" type="password" name="password"
                   class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                   placeholder="8자 이상" required autocomplete="new-password">
            @error('password')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">비밀번호 확인</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="auth-input"
                   placeholder="비밀번호를 다시 입력하세요" required autocomplete="new-password">
            @error('password_confirmation')
                <p class="auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="auth-submit">가입하기</button>
        </div>
    </form>

    <div class="auth-footer">
        <p>이미 계정이 있으신가요? <a href="{{ route('login') }}">로그인</a></p>
    </div>
</x-guest-layout>
