@extends('layouts.review')
@section('title', '비밀번호 재설정 — PAC-RUN')

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
    .auth-footer { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #E8EAEE; text-align: center; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-heading">
            <h1>새 비밀번호 설정</h1>
            <p>이메일로 받으신 링크를 통해 접속하셨습니다. 새로 사용할 비밀번호를 입력해 주세요.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <div class="auth-field">
                <label class="auth-label" for="password">새 비밀번호</label>
                <input id="password" type="password" name="password"
                       class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                       placeholder="8자 이상" required autofocus autocomplete="new-password">
                @error('password')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="password_confirmation">새 비밀번호 확인</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="auth-input" placeholder="비밀번호를 다시 입력해 주세요"
                       required autocomplete="new-password">
                @error('password_confirmation')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            @error('email')
                <p class="auth-field-error" style="margin-bottom: 1rem;">{{ $message }}</p>
            @enderror

            <button type="submit" class="auth-submit">비밀번호 재설정</button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}" class="auth-link">← 로그인으로 돌아가기</a>
        </div>
    </div>
</div>
@endsection
