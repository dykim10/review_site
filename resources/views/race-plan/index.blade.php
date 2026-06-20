@extends('layouts.review')

@section('title', '레이스 플랜 이력 — PAC-RUN')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('races.show', $edition->race_id) }}" class="text-sm text-rv-ink2 hover:text-rv-ink">← 대회로</a>
    <h1 class="text-xl font-700 text-rv-ink mt-4 mb-2">레이스 플랜 이력</h1>
    <p class="text-sm text-rv-ink2 mb-6">{{ $edition->name }} · {{ $edition->year }}년</p>

    @forelse($plans as $plan)
        <a href="{{ route('race-plan.show', $plan) }}"
           class="block mb-3 p-4 bg-rv-card border border-rv-line rounded-xl hover:border-rv-pink transition">
            <div class="text-sm font-600 text-rv-ink">
                목표 {{ $plan->input['goal_time'] ?? '-' }}
            </div>
            <div class="text-xs text-rv-ink2 mt-1">{{ $plan->created_at?->format('Y.m.d H:i') }}</div>
        </a>
    @empty
        <p class="text-sm text-rv-ink2">생성된 플랜이 없습니다.</p>
    @endforelse
</div>
@endsection
