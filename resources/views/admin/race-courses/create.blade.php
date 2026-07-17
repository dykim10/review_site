@extends('layouts.admin')
@section('title', 'GPX 코스 업로드 — PAC-RUN Admin')
@section('page-title', 'GPX 코스 업로드')

@section('content')
    <div class="adm-form-card">
        <form method="POST" action="{{ route('admin.race-courses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="adm-field">
                <label class="adm-label">연도별 대회 <span style="color:#DC2626;">*</span></label>
                <select name="race_edition_id" id="race_edition_id" required class="adm-input">
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
                <p id="course-type-hint" style="font-size:0.72rem;color:#9CA3AF;margin:0.35rem 0 0.5rem;display:none;"></p>
                <div id="course-type-radios" style="display:flex;gap:1.2rem;margin-top:0.6rem;flex-wrap:wrap;">
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
                <input type="file" name="gpx_file" accept=".gpx,.xml" required class="adm-input">
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.4rem;">지원 형식: .gpx (GPX/XML). 동일 코스 타입 재업로드 시 덮어씁니다.</p>
                @error('gpx_file')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label">출처</label>
                <select name="source" class="adm-input">
                    @php($sources = ['manual' => '수동 등록', 'official' => '공식', 'wari-gari' => '와리가리', 'goandrace' => '고앤레이스'])
                    @foreach($sources as $val => $label)
                        <option value="{{ $val }}" @selected(old('source', 'manual') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('source')<p class="adm-field-error">{{ $message }}</p>@enderror
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

    <script>
        (function () {
            const select = document.getElementById('race_edition_id');
            const radios = document.getElementById('course-type-radios');
            const hint = document.getElementById('course-type-hint');
            const fallback = [
                { course_type: 'FULL', label: '풀마라톤 (42.195km)' },
                { course_type: 'HALF', label: '하프마라톤 (21km)' },
                { course_type: '10K', label: '10K' },
            ];
            const labels = { FULL: '풀마라톤 (42.195km)', HALF: '하프마라톤 (21km)', '10K': '10K' };

            function renderTypes(types, empty) {
                radios.innerHTML = '';
                const list = (types && types.length) ? types : fallback.map(f => ({ course_type: f.course_type, name: f.label }));
                if (empty) {
                    hint.style.display = 'block';
                    hint.textContent = '이 연도별 대회에 참가 종목이 등록되지 않았습니다. 먼저 종목을 등록해주세요. (임시로 기본 코스 타입을 표시합니다)';
                } else if (!types || !types.length) {
                    hint.style.display = 'block';
                    hint.textContent = '매핑 가능한 종목(풀/하프/10K)이 없어 기본 코스 타입을 표시합니다.';
                } else {
                    hint.style.display = 'none';
                }
                list.forEach((t, i) => {
                    const label = document.createElement('label');
                    label.style.cssText = 'display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;';
                    const input = document.createElement('input');
                    input.type = 'radio';
                    input.name = 'course_type';
                    input.value = t.course_type;
                    input.required = true;
                    if (i === 0) input.checked = true;
                    label.appendChild(input);
                    label.appendChild(document.createTextNode(' ' + (labels[t.course_type] || t.name || t.course_type)));
                    radios.appendChild(label);
                });
            }

            async function loadCategories(editionId) {
                if (!editionId) {
                    renderTypes(null, false);
                    return;
                }
                try {
                    const res = await fetch(`{{ url('/admin/race-editions') }}/${editionId}/entry-categories`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('load failed');
                    const data = await res.json();
                    renderTypes(data.course_types || [], !!data.empty);
                } catch (e) {
                    renderTypes(null, false);
                }
            }

            select?.addEventListener('change', () => loadCategories(select.value));
            if (select?.value) loadCategories(select.value);
        })();
    </script>
@endsection
