@extends('layouts.admin')
@section('title', '대회 수정 — PAC-RUN Admin')
@section('page-title', '대회 수정')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('races-admin.races.update', $race) }}">
            @csrf @method('PUT')

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $race->name) }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">대회일 <span style="color:#DC2626;">*</span></label>
                    <input type="date" name="race_date" value="{{ old('race_date', $race->latestEdition?->race_date?->format('Y-m-d')) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time', $race->latestEdition?->race_time) }}" placeholder="예: 09:00" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">장소</label>
                    <input type="text" name="location" value="{{ old('location', $race->latestEdition?->location) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">도시</label>
                    <input type="text" name="city" value="{{ old('city', $race->city) }}" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">주최자</label>
                    <input type="text" name="organizer" value="{{ old('organizer', $race->organizer) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">참가비 (원) <span class="adm-label-hint">레거시·선택</span></label>
                    <input type="number" name="entry_fee" value="{{ old('entry_fee', $race->latestEdition?->entry_fee) }}" min="0" class="adm-input">
                </div>
            </div>

            @include('admin.races.partials.entry-categories', ['edition' => $race->latestEdition])

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">접수 시작일</label>
                    <input type="date" name="reg_start" value="{{ old('reg_start', $race->latestEdition?->reg_start?->format('Y-m-d')) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">접수 종료일</label>
                    <input type="date" name="reg_end" value="{{ old('reg_end', $race->latestEdition?->reg_end?->format('Y-m-d')) }}" class="adm-input">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">상태</label>
                <select name="status" class="adm-input">
                    @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $race->latestEdition?->status ?? '접수전') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label">거리 <span class="adm-label-hint">(쉼표로 구분)</span></label>
                <input type="text" name="distances_raw" value="{{ old('distances_raw', is_array($race->distances) ? implode(', ', $race->distances) : $race->distances) }}" class="adm-input">
                @error('distances_raw')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">공식 홈페이지</label>
                <input type="url" name="website_url" value="{{ old('website_url', $race->website_url) }}" class="adm-input">
            </div>

            <div class="adm-field">
                <label class="adm-label">기상청 지점코드 <span class="adm-label-hint">(비워두면 자동추론)</span></label>
                <input type="number" name="weather_stn_id" value="{{ old('weather_stn_id', $race->latestEdition?->weather_stn_id) }}" placeholder="예: 108" class="adm-input">
            </div>

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('races-admin.races.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">저장</button>
            </div>
        </form>
    </div>
@endsection
