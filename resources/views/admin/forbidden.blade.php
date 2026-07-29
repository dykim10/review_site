@extends('layouts.review')
@section('title', '접근 불가 — PAC-RUN')

@section('content')
<div style="max-width:420px;margin:4rem auto;padding:2rem;text-align:center;">
    <div style="display:inline-block;background:#E80043;color:#fff;font-size:0.65rem;font-weight:700;letter-spacing:0.12em;padding:0.3rem 0.65rem;border-radius:999px;margin-bottom:1.25rem;">PAC-RUN REVIEW</div>
    <div style="font-size:3.5rem;font-weight:800;color:#16181D;margin-bottom:0.5rem;">403</div>
    <h1 style="font-size:1.35rem;font-weight:700;margin-bottom:0.5rem;">접근 권한이 없습니다</h1>
    <p style="color:#5A6170;font-size:0.9rem;line-height:1.55;margin-bottom:1.75rem;">
        관리자 전용 페이지입니다.<br>
        일반 회원은 이 페이지에 접근할 수 없습니다.
    </p>
    <a href="{{ route('home') }}" style="display:inline-block;background:#E80043;color:#fff;font-weight:700;padding:0.7rem 1.4rem;border-radius:8px;text-decoration:none;">사이트로 돌아가기</a>
</div>
@endsection
