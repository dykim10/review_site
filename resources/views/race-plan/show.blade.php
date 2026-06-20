@extends('layouts.review')

@section('title', '레이스 플랜 — PAC-RUN')

@section('content')
@php $planData = $plan->plan_json ?? []; @endphp
<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('race-plan.index', $plan->raceEdition) }}" class="text-sm text-rv-ink2 hover:text-rv-ink">← 이력 목록</a>
    <h1 class="text-xl font-700 text-rv-ink mt-4 mb-2">{{ $planData['race_name'] ?? '레이스 플랜' }}</h1>
    <p class="text-sm text-rv-ink2 mb-4">목표 {{ $planData['goal_time'] ?? ($plan->input['goal_time'] ?? '-') }}</p>

    @if(!empty($planData['strategy_overview']))
        <div class="p-4 bg-rv-card border border-rv-line rounded-xl mb-4">
            <h2 class="text-sm font-600 text-rv-ink mb-2">전략 요약</h2>
            <p class="text-sm text-rv-ink2 leading-relaxed">{{ $planData['strategy_overview'] }}</p>
        </div>
    @endif

    @if(!empty($planData['weather_summary']))
        <div class="p-4 bg-rv-card border border-rv-line rounded-xl mb-4">
            <h2 class="text-sm font-600 text-rv-ink mb-2">날씨</h2>
            <p class="text-sm text-rv-ink2">{{ $planData['weather_summary'] }}</p>
        </div>
    @endif

    <p class="text-xs text-rv-ink2">생성 {{ $plan->created_at?->format('Y.m.d H:i') }}</p>
</div>
@endsection
