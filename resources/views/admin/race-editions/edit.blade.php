@extends('layouts.admin')
@section('title', '연도별 대회 수정 — PAC-RUN Admin')
@section('page-title', '연도별 대회 수정')

@section('content')
    @php
        $prefill = $clonePrefill ?? [];
        $v = function (string $key, $default = null) use ($prefill) {
            if (old($key) !== null) {
                return old($key);
            }
            if (array_key_exists($key, $prefill) && $prefill[$key] !== null && $prefill[$key] !== '') {
                return $prefill[$key];
            }
            return $default;
        };
    @endphp

    <div class="adm-form-card">
        @if($siblingEditions->count() > 1)
            <div style="margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid #E5E7EB;">
                <label class="adm-label">연도 선택</label>
                <select class="adm-input" style="max-width:220px;" onchange="if (this.value) window.location = this.value;">
                    @foreach($siblingEditions as $sibling)
                        <option value="{{ route('admin.race-editions.edit', $sibling) }}"
                            @selected($sibling->id === $edition->id)>
                            {{ $sibling->year }}년
                            @if($sibling->race_date)
                                ({{ $sibling->race_date->format('Y.m.d') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <p style="margin:0.4rem 0 0;font-size:0.75rem;color:#9CA3AF;">
                    edition #{{ $edition->id }}
                    @if($race)
                        · races #{{ $race->id }}
                    @endif
                </p>
            </div>
        @else
            <p style="margin:0 0 1rem;font-size:0.75rem;color:#9CA3AF;">
                edition #{{ $edition->id }}
                @if($race)
                    · races #{{ $race->id }}
                @endif
            </p>
        @endif

        <form method="POST" action="{{ route('admin.race-editions.update', $edition) }}" x-data="{ isDomestic: {{ $v('is_domestic', $edition->is_domestic ? '1' : '0') == '1' ? 'true' : 'false' }} }">
            @csrf @method('PUT')

            @include('admin.races.partials.race-master-fields', ['race' => $race])

            <div style="font-weight:700;font-size:0.9rem;margin:1.25rem 0 0.75rem;">
                {{ $edition->year }}년 연도별 대회
            </div>

            <div class="adm-field">
                <label class="adm-label">대회(마스터) <span style="color:#DC2626;">*</span></label>
                <select name="race_id" required class="adm-input">
                    @foreach($races as $r)
                        <option value="{{ $r->id }}" @selected($v('race_id', $edition->race_id) == $r->id)>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
                @error('race_id')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">대회명 <span style="color:#DC2626;">*</span></label>
                <input type="text" name="name" value="{{ $v('name', $edition->name) }}" required class="adm-input">
                @error('name')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">대회일</label>
                    <input type="date" name="race_date" value="{{ old('race_date', $edition->race_date?->format('Y-m-d')) }}" class="adm-input">
                    @if(!empty($prefill))
                        <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.35rem;">복제 프리필 시 대회일은 기존 값을 유지합니다. 필요하면 수정하세요.</p>
                    @endif
                </div>
                <div class="adm-field">
                    <label class="adm-label">개최 연도 <span style="color:#DC2626;">*</span></label>
                    <input type="number" name="year" value="{{ $v('year', $edition->year) }}" required min="1990" max="2100" class="adm-input">
                    @error('year')<p class="adm-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">시작 시간</label>
                    <input type="text" name="race_time" value="{{ $v('race_time', $edition->race_time) }}" placeholder="예: 09:00" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">접수 상태</label>
                    <select name="status" class="adm-input">
                        @foreach(['접수전','접수중','접수마감','대회종료','upcoming','ended'] as $s)
                            <option value="{{ $s }}" @selected($v('status', $edition->status) === $s)>
                                @if($s === 'upcoming') 예정 (upcoming)
                                @elseif($s === 'ended') 종료 (ended)
                                @else {{ $s }}
                                @endif
                            </option>
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
                <p class="adm-label-hint">종료(ended) 상태에서 후기 작성 허용. race_date 경과 시 스케줄러가 자동 true.</p>
            </div>

            <div class="adm-grid-2">
                <div class="adm-field">
                    <label class="adm-label">장소</label>
                    <input type="text" name="location" value="{{ $v('location', $edition->location) }}" class="adm-input">
                </div>
                <div class="adm-field">
                    <label class="adm-label">도시</label>
                    <input type="text" name="city" value="{{ $v('city', $edition->city ?? $race?->city) }}" class="adm-input">
                </div>
            </div>

            <div style="display:flex;gap:1.1rem;margin-bottom:1.1rem;align-items:flex-end;">
                <div style="flex:1;">
                    <label class="adm-label">국내/해외</label>
                    <div style="display:flex;gap:1.2rem;margin-top:0.5rem;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="1" @checked($v('is_domestic', $edition->is_domestic ? '1' : '0') == '1') x-on:change="isDomestic = true">
                            국내
                        </label>
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="is_domestic" value="0" @checked($v('is_domestic', $edition->is_domestic ? '1' : '0') == '0') x-on:change="isDomestic = false">
                            해외
                        </label>
                    </div>
                </div>
                <div style="flex:1;" x-show="!isDomestic">
                    <label class="adm-label">국가</label>
                    <input type="text" name="country" value="{{ $v('country', $edition->country) }}" class="adm-input">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">참가비 (원) <span class="adm-label-hint">레거시·선택</span></label>
                <input type="text" name="entry_fee" value="{{ $v('entry_fee', $edition->entry_fee) }}" class="adm-input">
            </div>

            @include('admin.partials.weather-stn-select', [
                'weatherStations' => $weatherStations,
                'selectedStnId' => $v('weather_stn_id', $edition->weather_stn_id),
            ])

            @include('admin.races.partials.entry-categories', ['edition' => $edition])

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
                <a href="{{ route('admin.race-editions.index') }}" class="adm-btn adm-btn-ghost">목록</a>
                @if($race)
                    <a href="{{ route('admin.races.index', ['q' => $race->name]) }}" class="adm-btn adm-btn-ghost">대회 검색</a>
                @endif
                <button type="submit" class="adm-btn adm-btn-primary">저장</button>
            </div>
        </form>
    </div>
@endsection
