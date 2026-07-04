@extends('layouts.review')
@section('title', '프로필 설정 — PAC-RUN')

@push('styles')
<style>
    .profile-wrap { max-width: 640px; margin: 0 auto; padding: 2.75rem 1.5rem 5rem; }
    .profile-title { font-size: 1.5rem; font-weight: 700; color: #16181D; margin-bottom: 2rem; }

    .p-card { background: #fff; border: 1px solid #E8EAEE; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.25rem; }
    .p-card-head { margin-bottom: 1.5rem; }
    .p-card-title { font-size: 1.05rem; font-weight: 700; color: #16181D; margin-bottom: 0.3rem; }
    .p-card-desc { font-size: 0.82rem; color: #5A6170; line-height: 1.6; }

    .field { margin-bottom: 1.2rem; }
    .field:last-child { margin-bottom: 0; }
    .field-label { display: block; font-size: 0.78rem; font-weight: 600; color: #5A6170; margin-bottom: 0.45rem; }
    .field-input {
        width: 100%; background: #F7F8FA; border: 1px solid #E8EAEE; border-radius: 8px;
        padding: 0.6rem 0.85rem; font-size: 0.9rem; color: #16181D; font-family: 'Pretendard', sans-serif;
        outline: none; transition: border-color 0.2s, background 0.2s;
    }
    .field-input:focus { border-color: #E80043; background: #fff; }
    .field-error { font-size: 0.75rem; color: #DC2626; margin-top: 0.35rem; }

    .btn-row { display: flex; align-items: center; gap: 0.9rem; margin-top: 1.5rem; }
    .btn-submit {
        padding: 0.6rem 1.5rem; background: #E80043; color: #fff; border: none; border-radius: 999px;
        font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: background 0.15s;
    }
    .btn-submit:hover { background: #C20038; }
    .btn-danger {
        padding: 0.6rem 1.4rem; background: #fff; color: #DC2626; border: 1.5px solid #FCA5A5; border-radius: 999px;
        font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: all 0.15s;
    }
    .btn-danger:hover { background: #FEF2F2; border-color: #DC2626; }
    .btn-danger-solid {
        padding: 0.6rem 1.4rem; background: #DC2626; color: #fff; border: none; border-radius: 999px;
        font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: background 0.15s;
    }
    .btn-danger-solid:hover { background: #B91C1C; }
    .btn-secondary {
        padding: 0.6rem 1.2rem; background: transparent; color: #5A6170; border: 1px solid #E8EAEE; border-radius: 999px;
        font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: 'Pretendard', sans-serif; transition: all 0.15s;
    }
    .btn-secondary:hover { color: #16181D; border-color: #9AA1AE; }

    .saved-msg { font-size: 0.8rem; color: #15803D; }
    .verify-box { font-size: 0.8rem; color: #5A6170; margin-top: 0.6rem; line-height: 1.6; }
    .verify-link { background: none; border: none; color: #E80043; text-decoration: underline; cursor: pointer; font-size: 0.8rem; font-family: inherit; padding: 0; }
    .verify-ok { color: #15803D; font-weight: 600; margin-top: 0.4rem; font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="profile-wrap">
    <h1 class="profile-title">프로필 설정</h1>
    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.request-password-reset-form')
    @include('profile.partials.delete-user-form')
</div>
@endsection
