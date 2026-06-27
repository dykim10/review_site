@extends('layouts.admin')
@section('title', '대회 등록 — PAC-RUN Admin')
@section('page-title', '대회 등록')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.races.store') }}">
            @csrf

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">대회일 <span style="color:#DC2626;">*</span></label>
                    <input type="date" name="race_date" value="{{ old('race_date') }}" class="adm-input">
                    @error('race_date')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time') }}" placeholder="예: 09:00" class="adm-input">
                    @error('race_time')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">장소</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">도시</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">주최자</label>
                    <input type="text" name="organizer" value="{{ old('organizer') }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">참가비 (원) <span class="adm-label-hint">레거시·선택</span></label>
                    <input type="number" name="entry_fee" value="{{ old('entry_fee') }}" min="0" class="adm-input">
                </div>
            </div>

            @include('admin.races.partials.entry-categories', ['edition' => null])

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">접수 시작일</label>
                    <input type="date" name="reg_start" value="{{ old('reg_start') }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">접수 종료일</label>
                    <input type="date" name="reg_end" value="{{ old('reg_end') }}" class="adm-input">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">상태</label>
                <select name="status" class="adm-input">
                    @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                        <option value="{{ $s }}" @selected(old('status', '접수전') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                @error('status')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">거리 <span class="adm-label-hint">(쉼표로 구분, 예: 5K,10K,하프,풀)</span></label>
                <input type="text" name="distances_raw" value="{{ old('distances_raw') }}" placeholder="5K,10K,하프,풀" class="adm-input">
                @error('distances_raw')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">공식 홈페이지</label>
                <input type="url" name="website_url" value="{{ old('website_url') }}" class="adm-input">
            </div>

            <div class="adm-field">
                <label class="adm-label">기상청 지점코드 <span class="adm-label-hint">(비워두면 자동추론)</span></label>
                <input type="number" name="weather_stn_id" value="{{ old('weather_stn_id') }}" placeholder="예: 108 (서울)" class="adm-input">
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">주요 코드: 서울 108 / 인천 112 / 수원 119 / 춘천 101 / 강릉 105 / 대전 133 / 광주 156 / 부산 159 / 제주 184</p>
            </div>

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('admin.races.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">등록</button>
            </div>
        </form>
    </div>
@endsection
