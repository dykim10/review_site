@extends('layouts.review')
@section('title', '로그인 — PAC-RUN')

@push('styles')
<style>
    .auth-wrap { max-width: 420px; margin: 0 auto; padding: 3.5rem 1.5rem 5rem; }
    .auth-card { background: #fff; border: 1px solid #E8EAEE; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(22,24,29,0.05); }

    .auth-heading { margin-bottom: 1.75rem; }
    .auth-heading h1 { font-size: 1.5rem; font-weight: 700; color: #16181D; margin-bottom: 0.3rem; }
    .auth-heading p { font-size: 0.8rem; color: #5A6170; }

    .auth-field { margin-bottom: 1.2rem; }
    .auth-label { display: block; font-size: 0.75rem; font-weight: 600; color: #5A6170; margin-bottom: 0.45rem; }
    .auth-input {
        display: block; width: 100%; background: #F7F8FA; border: 1px solid #E8EAEE; border-radius: 8px;
        padding: 0.6rem 0.85rem; font-size: 0.9rem; color: #16181D; font-family: 'Pretendard', sans-serif;
        outline: none; transition: border-color 0.2s, background 0.2s;
    }
    .auth-input:focus { border-color: #E80043; background: #fff; }
    .auth-input::placeholder { color: #9AA1AE; }
    .auth-input.error { border-color: #F87171; }
    .auth-field-error { font-size: 0.75rem; color: #DC2626; margin-top: 0.35rem; }

    .auth-check-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
    .auth-check-row input[type="checkbox"] { width: 15px; height: 15px; accent-color: #E80043; cursor: pointer; }
    .auth-check-row label { font-size: 0.8rem; color: #5A6170; cursor: pointer; }

    .auth-actions { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .auth-submit {
        padding: 0.6rem 1.5rem; background: #E80043; color: #fff; border: none; border-radius: 999px;
        font-size: 0.88rem; font-weight: 600; font-family: 'Pretendard', sans-serif; cursor: pointer; transition: background 0.15s;
    }
    .auth-submit:hover { background: #C20038; }
    .auth-link { font-size: 0.8rem; color: #5A6170; transition: color 0.15s; }
    .auth-link:hover { color: #E80043; }

    .auth-footer { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #E8EAEE; text-align: center; }
    .auth-footer p { font-size: 0.8rem; color: #5A6170; }
    .auth-footer a { color: #E80043; font-weight: 600; transition: color 0.15s; }
    .auth-footer a:hover { color: #C20038; }

    .status-msg {
        padding: 0.65rem 0.9rem; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px;
        font-size: 0.8rem; color: #15803D; margin-bottom: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
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
    </div>
</div>
@endsection
