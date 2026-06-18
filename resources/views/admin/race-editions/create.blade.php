@extends('layouts.admin')
@section('title', '대회 인스턴스 등록 — PAC-RUN Admin')
@section('page-title', '대회 인스턴스 등록')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.race-editions.store') }}" x-data="{ isDomestic: true }">
            @csrf

            <div class="adm-field">
                <label class="adm-label">원본 대회 연결 <span class="adm-label-hint">(선택 — 비워두면 독립)</span></label>
                <select name="race_id" class="adm-input">
                    <option value="">연결 안 함</option>
                    @foreach($races as $race)
                        <option value="{{ $race->id }}" @selected(old('race_id') == $race->id)>
                            {{ $race->name }}{{ $race->latestEdition ? ' ('.$race->latestEdition->year.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">대회일</label>
                    <input type="date" name="race_date" value="{{ old('race_date') }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">개최 연도 <span style="color:#DC2626;">*</span></label>
                    <input type="number" name="year" value="{{ old('year') }}" required min="1990" max="2100" placeholder="예: 2024" class="adm-input">
                    @error('year')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time') }}" placeholder="예: 09:00" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">상태</label>
                    <select name="status" class="adm-input">
                        @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                            <option value="{{ $s }}" @selected(old('status', '접수전') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
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

            <div style="display:flex;gap:1.1rem;margin-bottom:1.1rem;align-items:flex-end;">
                <div style="flex:1;">
                    <label class="adm-label">국내/해외</label>
                    <div style="display:flex;gap:1.2rem;margin-top:0.5rem;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="1" @checked(old('is_domestic', '1') === '1') x-on:change="isDomestic = true">
                            국내
                        </label>
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="0" @checked(old('is_domestic') === '0') x-on:change="isDomestic = false">
                            해외
                        </label>
                    </div>
                </div>
                <div style="flex:1;display:none;" x-show="!isDomestic">
                    <label class="adm-label">국가</label>
                    <input type="text" name="country" value="{{ old('country') }}" placeholder="예: 일본, 미국" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">참가비</label>
                    <input type="text" name="entry_fee" value="{{ old('entry_fee') }}" placeholder="예: 50,000원" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">기상청 지점코드</label>
                    <input type="number" name="weather_stn_id" value="{{ old('weather_stn_id') }}" placeholder="예: 108" class="adm-input">
                </div>
            </div>

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

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('admin.race-editions.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">등록</button>
            </div>
        </form>
    </div>
@endsection
