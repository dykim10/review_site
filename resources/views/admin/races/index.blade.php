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

        <form method="POST" action="{{ route('admin.wa-label.sync') }}" style="margin-top:1.1rem;display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.75rem;"
              onsubmit="return confirm('선택 시즌 WA Label 목록을 DB에 동기화합니다. 1~3분 소요될 수 있습니다. 계속할까요?')">
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
                <input type="checkbox" name="translate" value="1"> 한국어 번역 (Haiku)
            </label>
            <label style="display:flex;align-items:center;gap:0.35rem;font-size:0.8rem;color:#4B5563;padding-bottom:0.55rem;">
                <input type="checkbox" name="organiser" value="1"> 주최/공식 URL
            </label>
            <button type="submit" class="adm-btn adm-btn-primary">동기화 실행 (백그라운드)</button>
        </form>

        @if($waSyncStatuses->isNotEmpty())
            <div style="margin-top:0.85rem;font-size:0.78rem;color:#4B5563;line-height:1.6;">
                <strong>최근 동기화 상태</strong>
                @foreach($waSyncStatuses as $y => $st)
                    @php $status = $st['status'] ?? 'unknown'; @endphp
                    <div>
                        {{ $y }}:
                        @if($status === 'running')
                            <span style="color:#B45309;">진행 중…</span>
                        @elseif($status === 'done')
                            @php $r = $st['result'] ?? []; @endphp
                            <span style="color:#047857;">완료</span>
                            — 수집 {{ $r['total'] ?? '?' }} / 신규 {{ $r['inserted'] ?? '?' }} / 갱신 {{ $r['updated'] ?? '?' }}
                        @elseif($status === 'failed')
                            <span style="color:#B91C1C;">실패</span> — {{ Str::limit($st['error'] ?? '', 80) }}
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

    <div class="adm-card">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>대회명</th>
                    <th>대회일</th>
                    <th>도시</th>
                    <th>국내/해외</th>
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
                            <span class="adm-badge adm-badge-gray">{{ $race->latestEdition?->status ?? '접수전' }}</span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.races.edit', $race) }}" class="adm-link">수정</a>
                            <form method="POST" action="{{ route('admin.races.destroy', $race) }}" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn-danger" style="margin-left:0.6rem;">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="adm-td-empty">등록된 대회가 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem;">{{ $races->links() }}</div>
@endsection
