@extends('layouts.admin')
@section('title', 'GPX 코스 관리 — PAC-RUN Admin')
@section('page-title', 'GPX 코스 관리')

@section('topbar-action')
    <a href="{{ route('admin.race-courses.create') }}" class="adm-btn adm-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        GPX 업로드
    </a>
@endsection

@section('content')
    <div class="adm-card">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>대회</th>
                    <th>연도</th>
                    <th>코스</th>
                    <th>출처</th>
                    <th>공인</th>
                    <th>등록일</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td style="font-weight:600;">{{ $course->raceEdition?->race?->name ?? $course->raceEdition?->name ?? '-' }}</td>
                        <td class="adm-td-muted">{{ $course->raceEdition?->year }}</td>
                        <td>
                            <span class="adm-badge" style="background:{{ $course->course_type === 'FULL' ? '#DBEAFE' : ($course->course_type === 'HALF' ? '#DDD6FE' : '#F1F2F5') }};color:{{ $course->course_type === 'FULL' ? '#1D4ED8' : ($course->course_type === 'HALF' ? '#6366F1' : '#565D6B') }};">
                                {{ $course->course_type }}
                            </span>
                        </td>
                        <td class="adm-td-muted">{{ $course->source ?? '-' }}</td>
                        <td>{{ $course->is_certified ? '✓ 공인' : '-' }}</td>
                        <td class="adm-td-muted">{{ $course->created_at?->format('Y.m.d') }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.race-courses.edit', $course) }}" class="adm-link">수정</a>
                            <form method="POST" action="{{ route('admin.race-courses.destroy', $course) }}" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn-danger" style="margin-left:0.6rem;">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="adm-td-empty">등록된 GPX 파일이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem;">{{ $courses->links() }}</div>
@endsection
