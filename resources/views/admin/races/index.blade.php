@extends('layouts.admin')
@section('title', '대회 관리 — PAC-RUN Admin')
@section('page-title', '대회 관리')

@section('topbar-action')
    <a href="{{ route('admin.races.create') }}" class="adm-btn adm-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        대회 등록
    </a>
@endsection

@section('content')
    {{-- WA Label Road Races 수동 동기화 (운영: Swagger 대신 admin) --}}
    <div class="adm-form-card" style="margin-bottom:1.25rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div>
                <div style="font-weight:700;font-size:0.95rem;margin-bottom:0.35rem;">World Athletics Label Road Races</div>
                <p style="margin:0;color:#6B7280;font-size:0.8rem;line-height:1.5;max-width:520px;">
                    공식 GraphQL 캘린더 기준 시즌별 공인 대회를 <code>races</code> 카탈로그에 반영합니다.
                    <code>race_editions</code>는 생성하지 않습니다. 시즌마다 1회 실행 (2024 → 2025 → 2026 순 백필 권장).
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.wa-label.sync') }}" id="wa-sync-form" style="margin-top:1.1rem;display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.75rem;">
            @csrf
            <div class="adm-field" style="margin:0;min-width:120px;">
                <label class="adm-label" for="wa-year">시즌 (season)</label>
                <select id="wa-year" name="year" class="adm-input" required>
                    @foreach ([2026, 2025, 2024, 2023, 2022] as $y)
                        <option value="{{ $y }}" @selected($y === (int) date('Y'))>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <label style="display:flex;align-items:center;gap:0.35rem;font-size:0.8rem;color:#4B5563;padding-bottom:0.55rem;">
                <input type="checkbox" name="translate" id="wa-translate" value="1"> 한국어 번역 (Haiku)
            </label>
            <label style="display:flex;align-items:center;gap:0.35rem;font-size:0.8rem;color:#4B5563;padding-bottom:0.55rem;">
                <input type="checkbox" name="organiser" id="wa-organiser" value="1"> 주최/공식 URL
            </label>
            <button type="submit" class="adm-btn adm-btn-primary">동기화 실행 (백그라운드)</button>
        </form>
        <script>
            document.getElementById('wa-sync-form')?.addEventListener('submit', function (e) {
                const year = document.getElementById('wa-year')?.value;
                const tr = document.getElementById('wa-translate')?.checked;
                const org = document.getElementById('wa-organiser')?.checked;
                let msg = year + ' 시즌 WA Label 동기화를 시작합니다.';
                if (tr && org) {
                    msg += '\n\n⚠ 번역+주최 URL 모두 켜짐 → 15~30분 이상 소요될 수 있습니다.\n백필(최초)은 체크 없이 실행을 권장합니다.';
                } else if (tr || org) {
                    msg += '\n\n옵션 켜짐 → 5~15분 소요될 수 있습니다.';
                } else {
                    msg += '\n\n약 1~3분 소요 (백그라운드). 완료 후 페이지를 새로고침하세요.';
                }
                if (!confirm(msg)) e.preventDefault();
            });
        </script>

        @if($waSyncStatuses->isNotEmpty())
            <div style="margin-top:0.85rem;font-size:0.78rem;color:#4B5563;line-height:1.6;">
                <strong>최근 동기화 상태</strong>
                @foreach($waSyncStatuses as $y => $st)
                    @php $status = $st['status'] ?? 'unknown'; @endphp
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;margin-top:0.25rem;">
                        <span>
                            {{ $y }}:
                            @if($status === 'running')
                                <span style="color:#B45309;">진행 중…</span>
                            @elseif($status === 'cancelling')
                                <span style="color:#B45309;">중지·롤백 중…</span>
                            @elseif($status === 'cancelled')
                                @php $rb = $st['rollback'] ?? []; @endphp
                                <span style="color:#6B7280;">중지됨 (롤백)</span>
                                — 삭제 {{ $rb['deleted_inserts'] ?? 0 }} / 복원 {{ ($rb['restored_updates'] ?? 0) + ($rb['restored_decertified'] ?? 0) }}
                            @elseif($status === 'done')
                                @php $r = $st['result'] ?? []; @endphp
                                <span style="color:#047857;">완료</span>
                                — 수집 {{ $r['total'] ?? '?' }} / 신규 {{ $r['inserted'] ?? '?' }} / 갱신 {{ $r['updated'] ?? '?' }}
                            @elseif($status === 'failed')
                                <span style="color:#B91C1C;">실패</span> — {{ Str::limit($st['error'] ?? '', 80) }}
                            @endif
                        </span>
                        @if(in_array($status, ['running', 'cancelling'], true))
                            <form method="POST" action="{{ route('admin.wa-label.cancel') }}" style="display:inline;margin:0;"
                                  onsubmit="return confirm('{{ $y }} 시즌 동기화를 중지하고, 이번 세션에서 변경한 DB 내용을 롤백합니다. 계속할까요?')">
                                @csrf
                                <input type="hidden" name="year" value="{{ $y }}">
                                <button type="submit" class="adm-btn adm-btn-danger" style="padding:0.25rem 0.55rem;font-size:0.72rem;">중지·롤백</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <p style="margin:0.75rem 0 0;font-size:0.75rem;color:#9CA3AF;">
            EC2 SSH: <code>php artisan review:wa-label-sync 2024</code> (nginx 타임아웃 없음). 번역·주최 URL은 체크박스 또는 <code>--translate --organiser</code>.
        </p>

        <form method="POST" action="{{ route('admin.wa-label.preview') }}" style="margin-top:0.75rem;display:inline;">
            @csrf
            <input type="hidden" name="year" id="wa-preview-year" value="{{ date('Y') }}">
            <button type="submit" class="adm-btn adm-btn-ghost" onclick="document.getElementById('wa-preview-year').value=document.getElementById('wa-year').value">
                목록 건수만 미리보기 (DB 변경 없음)
            </button>
        </form>
    </div>

    {{-- 국내 Pilot 4 — race_editions 연도별 생성 --}}
    <div class="adm-form-card" style="margin-bottom:1.25rem;">
        <div style="font-weight:700;font-size:0.95rem;margin-bottom:0.35rem;">국내 Pilot 4 — Edition 생성</div>
        <p style="margin:0 0 0.85rem;color:#6B7280;font-size:0.8rem;line-height:1.5;max-width:720px;">
            서울/대구/경주/군산 <code>race_editions</code>를 <strong>선택한 연도마다</strong> 생성합니다 (4대회 × N년).
            <strong>races</strong>는 WA sync 카탈로그(공인 대회) 행에 매칭한 뒤 edition만 붙입니다 — pilot 전용 중복 race를 새로 만들지 않습니다.
            날짜 우선순위: catalog → marathongo → 미정.
            미정은 <a href="{{ route('admin.race-editions.index') }}" class="adm-link">edition 관리</a>에서 수동 입력.
        </p>

        @if(!empty($pilotCatalog))
            <div style="font-size:0.75rem;color:#6B7280;margin-bottom:0.85rem;padding:0.5rem 0.65rem;background:#F9FAFB;border-radius:6px;line-height:1.55;">
                <strong style="color:#374151;">catalog 등록 날짜</strong> (우선 적용):
                @foreach($pilotCatalog as $pc)
                    <div>{{ $pc['name'] }} — {{ $pc['catalog_years'] !== [] ? implode(', ', $pc['catalog_years']) : '없음' }}</div>
                @endforeach
            </div>
        @endif

        @if(!empty($pilotStatus))
            <div style="font-size:0.78rem;color:#4B5563;margin-bottom:0.85rem;line-height:1.6;">
                @foreach($pilotStatus as $ps)
                    <div>
                        {{ $ps['name'] }}
                        @if(!empty($ps['race_name']) && $ps['race_name'] !== $ps['name'])
                            <span class="adm-td-muted">→ {{ $ps['race_name'] }}</span>
                        @endif
                        @if($ps['race_id'])
                            <span class="adm-td-muted">(race #{{ $ps['race_id'] }})</span>
                            — editions:
                            @if(count($ps['edition_years']))
                                {{ implode(', ', $ps['edition_years']) }}
                            @else
                                <span style="color:#B45309;">없음</span>
                            @endif
                        @else
                            <span style="color:#9CA3AF;">race 미생성</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $pilotYearOptions = range((int) date('Y') + 1, 2020);
            $defaultPilotYears = [(int) date('Y'), (int) date('Y') - 1];
        @endphp

        <form method="POST" action="{{ route('admin.pilot-editions.preview') }}" id="pilot-edition-form" style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">
            @csrf
            <div class="adm-field" style="margin:0;">
                <span class="adm-label">생성할 연도 (복수 선택)</span>
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:0.35rem;max-width:420px;">
                    @foreach($pilotYearOptions as $y)
                        <label style="font-size:0.78rem;color:#374151;display:flex;align-items:center;gap:0.25rem;">
                            <input type="checkbox" name="years[]" value="{{ $y }}"
                                @checked(in_array($y, $defaultPilotYears, true))>
                            {{ $y }}
                        </label>
                    @endforeach
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:0.35rem;font-size:0.8rem;color:#4B5563;padding-bottom:0.55rem;">
                <input type="checkbox" name="fetch_dates" value="1" checked>
                catalog 없는 연도 marathongo 날짜 조회
            </label>
            <button type="submit" class="adm-btn adm-btn-ghost">미리보기</button>
            <button type="submit" formaction="{{ route('admin.pilot-editions.provision') }}" class="adm-btn adm-btn-primary"
                    onclick="return confirm('선택 연도의 pilot edition 4개×N년을 생성/갱신합니다. 계속할까요?')">
                Edition 생성
            </button>
        </form>

        @if(session('pilot_preview'))
            <div style="margin-top:1rem;overflow-x:auto;">
                <div style="font-size:0.8rem;font-weight:600;margin-bottom:0.4rem;">미리보기 (DB 변경 없음)</div>
                <table class="adm-table" style="font-size:0.78rem;">
                    <thead>
                        <tr>
                            <th>대회</th><th>연도</th><th>날짜</th><th>출처</th><th>status</th><th>DB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('pilot_preview') as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['year'] }}</td>
                                <td @if(empty($row['race_date'])) style="color:#B45309;font-weight:600;" @endif>
                                    {{ $row['race_date'] ?? '미정' }}
                                    @if(empty($row['race_date']) && ($row['date_source'] ?? '') === 'null')
                                        <span style="font-weight:400;color:#9CA3AF;"> (catalog·marathongo 없음)</span>
                                    @endif
                                </td>
                                <td>{{ $row['date_source'] }}</td>
                                <td>{{ $row['status'] }}</td>
                                <td>{{ ($row['exists'] ?? false) ? '있음' : '신규' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(session('pilot_provision'))
            <div style="margin-top:1rem;overflow-x:auto;">
                <div style="font-size:0.8rem;font-weight:600;margin-bottom:0.4rem;">생성 결과</div>
                <table class="adm-table" style="font-size:0.78rem;">
                    <thead>
                        <tr>
                            <th>대회</th><th>연도</th><th>edition</th><th>날짜</th><th>status</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('pilot_provision') as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['year'] }}</td>
                                <td>#{{ $row['edition_id'] }}</td>
                                <td>{{ $row['race_date'] ?? '미정' }}</td>
                                <td>{{ $row['status'] }}</td>
                                <td>{{ $row['action'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p style="margin:0.5rem 0 0;font-size:0.75rem;color:#6B7280;">
                    미정 날짜는 <a href="{{ route('admin.race-editions.index') }}" class="adm-link">edition 관리</a>에서 수동 입력.
                </p>
            </div>
        @endif
    </div>

    {{-- GPX 스텁 — edition 생성과 별도 --}}
    <div class="adm-form-card" style="margin-bottom:1.25rem;">
        <div style="font-weight:700;font-size:0.95rem;margin-bottom:0.35rem;">GPX 코스 스텁 (선택)</div>
        <p style="margin:0 0 0.85rem;color:#6B7280;font-size:0.8rem;line-height:1.5;max-width:720px;">
            Edition 생성과 <strong>별도</strong>입니다. <strong>한 연도</strong>를 고르면, 해당 연도의
            <strong>종료(ended)</strong> pilot edition 4개에 FULL 코스 GPX URL 스텁을 등록합니다.
            (날짜가 catalog에 있어 ended인 연도만 — 예: 2025)
        </p>
        <form method="POST" action="{{ route('admin.pilot-editions.attach-gpx') }}" style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">
            @csrf
            <div class="adm-field" style="margin:0;min-width:120px;">
                <label class="adm-label" for="pilot-gpx-year">GPX 등록 연도</label>
                <select id="pilot-gpx-year" name="gpx_year" class="adm-input" required>
                    @foreach($pilotYearOptions as $y)
                        <option value="{{ $y }}" @selected($y === (int) date('Y') - 1)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="adm-btn adm-btn-ghost"
                    onclick="return confirm('선택 연도의 종료된 pilot edition에 GPX 스텁을 등록합니다.')">
                GPX 스텁 등록
            </button>
        </form>

        @if(session('pilot_gpx'))
            <div style="margin-top:1rem;overflow-x:auto;">
                <table class="adm-table" style="font-size:0.78rem;">
                    <thead>
                        <tr><th>대회</th><th>연도</th><th>edition</th><th>결과</th><th>사유</th></tr>
                    </thead>
                    <tbody>
                        @foreach(session('pilot_gpx') as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['year'] }}</td>
                                <td>{{ isset($row['edition_id']) ? '#'.$row['edition_id'] : '—' }}</td>
                                <td>{{ $row['action'] }}</td>
                                <td>{{ $row['reason'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="adm-form-card" style="margin-bottom:1.25rem;">
        <form method="GET" action="{{ route('admin.races.index') }}" style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">
            <div class="adm-field" style="margin:0;flex:1;min-width:220px;">
                <label class="adm-label" for="race-search-q">대회 검색</label>
                <input type="search" id="race-search-q" name="q" value="{{ request('q') }}"
                       placeholder="대회명·영문명·도시·주최" class="adm-input" autocomplete="off">
            </div>
            <div class="adm-field" style="margin:0;min-width:140px;">
                <label class="adm-label" for="race-published">공개 여부</label>
                <select id="race-published" name="published" class="adm-input" onchange="this.form.submit()">
                    <option value="">전체</option>
                    <option value="1" @selected(request('published') === '1')>공개</option>
                    <option value="0" @selected(request('published') === '0')>비공개</option>
                </select>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">검색</button>
            @if(request()->filled('q') || request()->filled('published'))
                <a href="{{ route('admin.races.index') }}" class="adm-btn adm-btn-ghost">초기화</a>
            @endif
        </form>
        @if(request()->filled('q') || request()->filled('published'))
            <p style="margin:0.65rem 0 0;font-size:0.78rem;color:#6B7280;">
                필터 결과 — {{ $races->total() }}건
            </p>
        @endif
    </div>

    <div class="adm-card">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>대회명</th>
                    <th>대회일</th>
                    <th>도시</th>
                    <th>국내/해외</th>
                    <th>공개</th>
                    <th>상태</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($races as $race)
                    <tr>
                        <td>
                            <a href="{{ route('races.show', $race) }}" class="adm-link" style="color:#1A1D24;font-weight:600;">{{ $race->name }}</a>
                        </td>
                        <td class="adm-td-muted">{{ $race->latestEdition?->race_date?->format('Y.m.d') ?? '-' }}</td>
                        <td class="adm-td-muted">{{ $race->city ?? '-' }}</td>
                        <td>
                            <span class="adm-badge {{ $race->is_domestic ? 'adm-badge-green' : 'adm-badge-blue' }}">
                                {{ $race->is_domestic ? '국내' : '해외' }}
                            </span>
                        </td>
                        <td>
                            @if($race->is_published)
                                <span class="adm-badge adm-badge-green">공개</span>
                            @else
                                <span class="adm-badge adm-badge-gray">비공개</span>
                            @endif
                        </td>
                        <td>
                            <span class="adm-badge adm-badge-gray">{{ $race->latestEdition?->status ?? '접수전' }}</span>
                        </td>
                        <td style="text-align:right;">
                            @if($race->latestEdition)
                                <a href="{{ route('admin.race-editions.edit', $race->latestEdition) }}" class="adm-link">수정</a>
                            @else
                                <a href="{{ route('admin.races.edit', $race) }}" class="adm-link">수정</a>
                            @endif
                            <form method="POST" action="{{ route('admin.races.destroy', $race) }}" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn-danger" style="margin-left:0.6rem;">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="adm-td-empty">
                            @if(request()->filled('q') || request()->filled('published'))
                                조건에 해당하는 대회가 없습니다.
                            @else
                                등록된 대회가 없습니다.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem;">{{ $races->links() }}</div>
@endsection
