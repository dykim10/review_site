@extends('layouts.admin')
@section('title', '대회 등록 — PAC-RUN Admin')
@section('page-title', '대회 등록')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.races.store') }}" x-data="{ isDomestic: true }">
            @csrf

            <div style="font-weight:700;font-size:0.95rem;margin-bottom:0.35rem;">대회 카탈로그</div>
            <p style="margin:0 0 1rem;font-size:0.78rem;color:#6B7280;line-height:1.5;">
                연도와 무관한 마스터 정보입니다. 대표 코스는 목록 뱃지·필터용입니다.
            </p>

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">도시</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">주최자</label>
                    <input type="text" name="organizer" value="{{ old('organizer') }}" class="adm-input">
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
                    <input type="text" name="country" value="{{ old('country') }}" class="adm-input">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">공식 홈페이지</label>
                <input type="url" name="website_url" value="{{ old('website_url') }}" class="adm-input">
            </div>

            <div class="adm-field">
                <label class="adm-label">대표 코스 <span class="adm-label-hint">(쉼표로 구분, 1~2개 권장)</span></label>
                <input type="text" name="distances_raw" value="{{ old('distances_raw') }}" placeholder="풀,하프" class="adm-input">
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">
                    이 대회를 대표하는 코스(목록 뱃지·필터용). 연도별 실제 종목·참가비는 아래 연도별 대회에서 입력합니다.
                </p>
            </div>

            <div class="adm-field">
                <label class="adm-label" style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', '1') == '1')>
                    목록 공개
                </label>
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">
                    체크 시 공개 대회 목록에 노출되고 연도 자동 전환 대상이 됩니다. 연도별 대회가 등록되면 자동으로도 승격됩니다.
                </p>
            </div>

            <div style="border-top:1px solid #E5E7EB;margin:1.75rem 0 1rem;padding-top:1.5rem;">
                <div style="font-weight:700;font-size:0.95rem;margin-bottom:0.35rem;">첫 연도별 대회 정보</div>
                <p style="margin:0 0 1rem;font-size:0.78rem;color:#6B7280;line-height:1.5;">
                    마스터와 함께 첫 개최 연도 데이터를 만듭니다. 개최 연도는 필수입니다.
                </p>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">개최 연도 <span style="color:#DC2626;">*</span></label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="1990" max="2100" class="adm-input">
                    @error('year')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="adm-field">
                    <label class="adm-label">대회일</label>
                    <input type="date" name="race_date" value="{{ old('race_date') }}" class="adm-input">
                    @error('race_date')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time') }}" placeholder="예: 09:00" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">장소</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="adm-input">
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

            <div class="adm-field">
                <label class="adm-label">상태</label>
                <select name="status" class="adm-input">
                    @foreach(['upcoming' => '예정 (upcoming)', 'ended' => '종료 (ended)', '접수전' => '접수전', '접수중' => '접수중', '접수마감' => '접수마감'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', 'upcoming') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @include('admin.partials.weather-stn-select', [
                'weatherStations' => $weatherStations,
                'selectedStnId' => old('weather_stn_id'),
            ])

            @include('admin.races.partials.entry-categories', ['edition' => null])

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('admin.races.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">등록</button>
            </div>
        </form>
    </div>
@endsection
