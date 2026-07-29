@extends('layouts.admin')
@section('title', 'GPX 코스 수정 — PAC-RUN Admin')
@section('page-title', 'GPX 코스 수정')

@section('content')
    @php
        $edition = $course->raceEdition;
        $courseLabels = ['FULL' => '풀마라톤 (42.195km)', 'HALF' => '하프마라톤 (21km)', '10K' => '10K'];
        $sources = ['manual' => '수동 등록', 'official' => '공식', 'wari-gari' => '와리가리', 'goandrace' => '고앤레이스'];
    @endphp

    <div class="adm-form-card">
        <div style="margin-bottom:1.25rem;padding:0.85rem 1rem;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;font-size:0.78rem;color:#565D6B;">
            <div><strong>대회:</strong> {{ $edition?->race?->name ?? $edition?->name ?? '-' }} ({{ $edition?->year }})</div>
            <div style="margin-top:0.35rem;"><strong>코스:</strong> {{ $courseLabels[$course->course_type] ?? $course->course_type }}</div>
            @if($course->gpx_url)
                <div style="margin-top:0.35rem;word-break:break-all;">
                    <strong>현재 GPX:</strong>
                    <a href="{{ $course->gpx_url }}" target="_blank" rel="noopener" class="adm-link">{{ $course->gpx_url }}</a>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('races-admin.race-courses.update', $course) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="adm-field">
                <label class="adm-label">GPX 파일 교체</label>
                <input type="file" name="gpx_file" accept=".gpx,.xml" class="adm-input">
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">
                    새 파일을 선택하면 기존 GPX·고도·좌표 데이터가 교체됩니다. 변경 없으면 비워두세요.
                </p>
                @error('gpx_file')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">출처</label>
                <select name="source" class="adm-input">
                    @foreach($sources as $val => $label)
                        <option value="{{ $val }}" @selected(old('source', $course->source ?? 'manual') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('source')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                    <input type="hidden" name="is_certified" value="0">
                    <input type="checkbox" name="is_certified" value="1" @checked(old('is_certified', $course->is_certified))>
                    공인 코스
                </label>
            </div>

            <div class="adm-field">
                <label class="adm-label">공인 인증일</label>
                <input type="date" name="certified_at" value="{{ old('certified_at', $course->certified_at?->format('Y-m-d')) }}" class="adm-input">
                @error('certified_at')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('races-admin.race-courses.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">저장</button>
            </div>
        </form>
    </div>
@endsection
