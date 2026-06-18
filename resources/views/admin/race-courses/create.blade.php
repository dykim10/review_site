@extends('layouts.admin')
@section('title', 'GPX 코스 업로드 — PAC-RUN Admin')
@section('page-title', 'GPX 코스 업로드')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.race-courses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="adm-field">
                <label class="adm-label">대회 인스턴스 <span style="color:#DC2626;">*</span></label>
                <select name="race_edition_id" required class="adm-input">
                    <option value="">선택하세요</option>
                    @foreach($editions as $edition)
                        <option value="{{ $edition->id }}" @selected(old('race_edition_id') == $edition->id)>
                            {{ $edition->race?->name ?? $edition->name }}
                            ({{ $edition->year }}{{ $edition->race_date ? ', ' . \Carbon\Carbon::parse($edition->race_date)->format('m/d') : '' }})
                        </option>
                    @endforeach
                </select>
                @error('race_edition_id')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">코스 타입 <span style="color:#DC2626;">*</span></label>
                <div style="display:flex;gap:1.2rem;margin-top:0.6rem;">
                    @foreach(['FULL' => '풀마라톤 (42.195km)', 'HALF' => '하프마라톤 (21km)', '10K' => '10K'] as $val => $label)
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="course_type" value="{{ $val }}" @checked(old('course_type') === $val) required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('course_type')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">GPX 파일 업로드 <span style="color:#DC2626;">*</span></label>
                <input type="file" name="gpx_file" accept=".gpx,.tcx" required class="adm-input">
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">지원 형식: .gpx, .tcx</p>
                @error('gpx_file')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">출처</label>
                <input type="text" name="source" value="{{ old('source') }}" placeholder="예: wari-gari, goandrace, 공식" class="adm-input">
            </div>

            <div class="adm-field">
                <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                    <input type="checkbox" name="is_certified" value="1" @checked(old('is_certified'))>
                    공인 코스
                </label>
            </div>

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('admin.race-courses.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">업로드</button>
            </div>
        </form>
    </div>
@endsection
