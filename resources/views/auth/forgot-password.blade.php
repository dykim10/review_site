@extends('layouts.review')
@section('title', '비밀번호 찾기 — PAC-RUN')

@push('styles')
<style>
    .auth-wrap { max-width: 420px; margin: 0 auto; padding: 3.5rem 1.5rem 5rem; }
    .auth-card { background: #fff; border: 1px solid #E8EAEE; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(22,24,29,0.05); }
    .auth-heading { margin-bottom: 1.75rem; }
    .auth-heading h1 { font-size: 1.5rem; font-weight: 700; color: #16181D; margin-bottom: 0.3rem; }
    .auth-heading p { font-size: 0.8rem; color: #5A6170; line-height: 1.6; }
    .auth-field { margin-bottom: 1.2rem; }
    .auth-label { display: block; font-size: 0.75rem; font-weight: 600; color: #5A6170; margin-bottom: 0.45rem; }
    .auth-input {
        display: block; width: 100%; background: #F7F8FA; border: 1px solid #E8EAEE; border-radius: 8px;
        padding: 0.6rem 0.85rem; font-size: 0.9rem; color: #16181D; font-family: 'Pretendard', sans-serif;
        outline: none; transition: border-color 0.2s, background 0.2s;
    }
    .auth-input:focus { border-color: #E80043; background: #fff; }
    .auth-input.error { border-color: #F87171; }
    .auth-field-error { font-size: 0.75rem; color: #DC2626; margin-top: 0.35rem; }
    .auth-submit {
        width: 100%; padding: 0.65rem 1.5rem; background: #E80043; color: #fff; border: none; border-radius: 999px;
        font-size: 0.88rem; font-weight: 600; font-family: 'Pretendard', sans-serif; cursor: pointer; transition: background 0.15s;
    }
    .auth-submit:hover { background: #C20038; }
    .auth-link { font-size: 0.8rem; color: #5A6170; transition: color 0.15s; }
    .auth-link:hover { color: #E80043; }
    .status-msg {
        padding: 0.65rem 0.9rem; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px;
        font-size: 0.8rem; color: #15803D; margin-bottom: 1.25rem;
    }
    .auth-footer { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #E8EAEE; text-align: center; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        @if(session('status'))
            <div class="status-msg">이메일을 발송했습니다. 받은편지함을 확인해 주세요.</div>
        @endif

        <div class="auth-heading">
            <h1>비밀번호 찾기</h1>
            <p>가입한 이메일 주소를 입력하시면 비밀번호 재설정 링크를 보내드립니다.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="auth-field">
                <label class="auth-label" for="email">이메일</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                       placeholder="가입 시 사용한 이메일" required autofocus autocomplete="email">
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-submit">재설정 링크 발송</button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}" class="auth-link">← 로그인으로 돌아가기</a>
        </div>
    </div>
</div>
@endsection
