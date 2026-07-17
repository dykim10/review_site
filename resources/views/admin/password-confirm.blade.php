@extends('layouts.review')
@section('title', '관리자 확인 — PAC-RUN')

@push('styles')
<style>
    .auth-wrap { max-width: 420px; margin: 0 auto; padding: 3.5rem 1.5rem 5rem; }
    .auth-card { background: #fff; border: 1px solid #E8EAEE; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(22,24,29,0.05); }
    .auth-badge {
        display: inline-block; background: #E80043; color: #fff; font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase; padding: 0.3rem 0.65rem; border-radius: 999px; margin-bottom: 1rem;
    }
    .auth-heading { margin-bottom: 1.75rem; text-align: center; }
    .auth-heading h1 { font-size: 1.5rem; font-weight: 700; color: #16181D; margin-bottom: 0.45rem; }
    .auth-heading p { font-size: 0.85rem; color: #5A6170; line-height: 1.55; }
    .auth-alert {
        background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; border-radius: 8px;
        padding: 0.7rem 1rem; font-size: 0.83rem; margin-bottom: 1.25rem;
    }
    .auth-field { margin-bottom: 1.2rem; }
    .auth-label { display: block; font-size: 0.75rem; font-weight: 600; color: #5A6170; margin-bottom: 0.45rem; }
    .auth-input {
        display: block; width: 100%; background: #F7F8FA; border: 1px solid #E8EAEE; border-radius: 8px;
        padding: 0.6rem 0.85rem; font-size: 0.9rem; color: #16181D; font-family: 'Pretendard', sans-serif;
        outline: none; transition: border-color 0.2s, background 0.2s;
    }
    .auth-input:focus { border-color: #E80043; background: #fff; }
    .auth-input::placeholder { color: #9AA1AE; }
    .auth-submit {
        display: block; width: 100%; padding: 0.75rem 1.4rem; background: #E80043; color: #fff; border: none;
        border-radius: 8px; font-size: 0.9rem; font-weight: 700; font-family: 'Pretendard', sans-serif;
        cursor: pointer; transition: background 0.15s;
    }
    .auth-submit:hover { background: #C20038; }
    .auth-back { display: block; text-align: center; margin-top: 1.25rem; font-size: 0.82rem; color: #9AA1AE; }
    .auth-back:hover { color: #5A6170; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-heading">
            <span class="auth-badge">PAC-RUN REVIEW</span>
            <h1>관리자 확인</h1>
            <p>보안 구역입니다. 계속하려면<br>비밀번호를 다시 입력해 주세요.</p>
        </div>

        @if ($errors->any())
            <div class="auth-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.confirm.store') }}">
            @csrf
            <div class="auth-field">
                <label class="auth-label" for="password">비밀번호</label>
                <input
                    id="password"
                    class="auth-input"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="현재 비밀번호를 입력하세요"
                >
            </div>
            <button type="submit" class="auth-submit">확인 후 관리자 패널 이동</button>
        </form>

        <a href="{{ route('home') }}" class="auth-back">사이트로 돌아가기</a>
    </div>
</div>
@endsection
