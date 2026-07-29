@extends('layouts.admin')
@section('title', '연도별 대회 등록 — PAC-RUN Admin')
@section('page-title', '연도별 대회 등록')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('races-admin.race-editions.store') }}" x-data="{ isDomestic: {{ old('is_domestic', '1') === '1' ? 'true' : 'false' }} }">
            @csrf

            <div class="adm-field">
                <label class="adm-label">대회(마스터) <span style="color:#DC2626;">*</span></label>
                <select name="race_id" required class="adm-input">
                    <option value="">선택하세요</option>
                    @foreach($races as $race)
                        <option value="{{ $race->id }}" @selected(old('race_id') == $race->id)>
                            {{ $race->name }}{{ $race->latestEdition ? ' ('.$race->latestEdition->year.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('race_id')<p class="adm-field-error">{{ $message }}</p>@enderror
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
                    <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.35rem;">전년도 복제 시 비워 둡니다. 새로 입력하세요.</p>
                </div>
                <div class="adm-field">
                    <label class="adm-label">개최 연도 <span style="color:#DC2626;">*</span></label>
                    <input type="number" name="year" value="{{ old('year') }}" required min="1990" max="2100" placeholder="예: 2026" class="adm-input">
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
                        @foreach(['upcoming' => '예정 (upcoming)', 'ended' => '종료 (ended)', '접수전' => '접수전', '접수중' => '접수중', '접수마감' => '접수마감'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', 'upcoming') === $val)>{{ $label }}</option>
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
                <div style="flex:1;" x-show="!isDomestic">
                    <label class="adm-label">국가</label>
                    <input type="text" name="country" value="{{ old('country') }}" placeholder="예: 일본, 미국" class="adm-input">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">참가비 (원) <span class="adm-label-hint">레거시·선택</span></label>
                <input type="text" name="entry_fee" value="{{ old('entry_fee') }}" class="adm-input">
            </div>

            @include('admin.partials.weather-stn-select', [
                'weatherStations' => $weatherStations,
                'selectedStnId' => old('weather_stn_id'),
            ])

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

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('races-admin.race-editions.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">등록</button>
            </div>
        </form>
    </div>
@endsection
