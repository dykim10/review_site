@extends('layouts.admin')
@section('title', '대회 인스턴스 수정 — PAC-RUN Admin')
@section('page-title', '대회 인스턴스 수정')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.race-editions.update', $edition) }}" x-data="{ isDomestic: {{ old('is_domestic', $edition->is_domestic ? '1' : '0') }} === '1' }">
            @csrf @method('PUT')

            <div class="adm-field">
                <label class="adm-label">원본 대회 연결 <span class="adm-label-hint">(선택)</span></label>
                <select name="race_id" class="adm-input">
                    <option value="">연결 안 함</option>
                    @foreach($races as $race)
                        <option value="{{ $race->id }}" @selected(old('race_id', $edition->race_id) == $race->id)>
                            {{ $race->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $edition->name) }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">대회일</label>
                    <input type="date" name="race_date" value="{{ old('race_date', $edition->race_date?->format('Y-m-d')) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">개최 연도 <span style="color:#DC2626;">*</span></label>
                    <input type="number" name="year" value="{{ old('year', $edition->year) }}" required min="1990" max="2100" class="adm-input">
                    @error('year')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time', $edition->race_time) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">생명주기 상태</label>
                    <select name="status" class="adm-input">
                        @foreach(['upcoming' => '예정 (upcoming)', 'ended' => '종료 (ended)'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $edition->status) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label" style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                    <input type="hidden" name="is_review_open" value="0">
                    <input type="checkbox" name="is_review_open" value="1"
                           @checked(old('is_review_open', $edition->is_review_open))>
                    후기 작성 개방 (is_review_open)
                </label>
                <p class="adm-label-hint">ended 상태에서 후기 작성 허용. race_date 경과 시 스케줄러가 자동 true.</p>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">장소</label>
                    <input type="text" name="location" value="{{ old('location', $edition->location) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">도시</label>
                    <input type="text" name="city" value="{{ old('city', $edition->city) }}" class="adm-input">
                </div>
            </div>

            <div style="display:flex;gap:1.1rem;margin-bottom:1.1rem;align-items:flex-end;">
                <div style="flex:1;">
                    <label class="adm-label">국내/해외</label>
                    <div style="display:flex;gap:1.2rem;margin-top:0.5rem;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="1" @checked(old('is_domestic', $edition->is_domestic) == '1' || old('is_domestic', (int)$edition->is_domestic) == 1) x-on:change="isDomestic = true">
                            국내
                        </label>
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="0" @checked(old('is_domestic', $edition->is_domestic) == '0' || old('is_domestic', (int)$edition->is_domestic) == 0) x-on:change="isDomestic = false">
                            해외
                        </label>
                    </div>
                </div>
                <div style="flex:1;" x-show="!isDomestic">
                    <label class="adm-label">국가</label>
                    <input type="text" name="country" value="{{ old('country', $edition->country) }}" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">참가비</label>
                    <input type="text" name="entry_fee" value="{{ old('entry_fee', $edition->entry_fee) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">기상청 지점코드</label>
                    <input type="number" name="weather_stn_id" value="{{ old('weather_stn_id', $edition->weather_stn_id) }}" class="adm-input">
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">접수 시작일</label>
                    <input type="date" name="reg_start" value="{{ old('reg_start', $edition->reg_start?->format('Y-m-d')) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">접수 종료일</label>
                    <input type="date" name="reg_end" value="{{ old('reg_end', $edition->reg_end?->format('Y-m-d')) }}" class="adm-input">
                </div>
            </div>

            <div style="display:flex;gap:0.9rem;justify-content:flex-end;margin-top:1.75rem;">
                <a href="{{ route('admin.race-editions.index') }}" class="adm-btn adm-btn-ghost">취소</a>
                <button type="submit" class="adm-btn adm-btn-primary">저장</button>
            </div>
        </form>
    </div>
@endsection
