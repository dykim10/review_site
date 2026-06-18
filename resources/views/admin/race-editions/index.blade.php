@extends('layouts.admin')
@section('title', '대회 인스턴스 관리 — PAC-RUN Admin')
@section('page-title', '대회 인스턴스 관리')

@section('topbar-action')
    <a href="{{ route('admin.race-editions.create') }}" class="adm-btn adm-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        인스턴스 등록
    </a>
@endsection

@section('content')
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:10px;padding:1.2rem;margin-bottom:1.5rem;">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:0.8rem;align-items:flex-end;">
            <div>
                <label class="adm-label">국내/해외</label>
                <select name="is_domestic" class="adm-input" onchange="this.form?.submit()">
                    <option value="">전체</option>
                    <option value="1" @selected(request('is_domestic') === '1')>국내</option>
                    <option value="0" @selected(request('is_domestic') === '0')>해외</option>
                </select>
            </div>
            <div>
                <label class="adm-label">연도</label>
                <select name="year" class="adm-input" onchange="this.form?.submit()">
                    <option value="">전체</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}년</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-label">대회명 검색</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="검색" class="adm-input" onchange="this.form?.submit()">
            </div>
            <div>
                <a href="{{ route('admin.race-editions.index') }}" class="adm-btn adm-btn-ghost" style="width:100%;">초기화</a>
            </div>
        </div>
    </div>

    <div class="adm-card">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>대회명</th>
                    <th>연도</th>
                    <th>대회일</th>
                    <th>도시</th>
                    <th>구분</th>
                    <th>상태</th>
                    <th style="text-align:center;">리뷰</th>
                    <th style="text-align:center;">날씨</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($editions as $edition)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $edition->name }}</div>
                            @if($edition->race)
                                <div style="font-size:0.72rem;color:#9CA3AF;">races #{{ $edition->race_id }}</div>
                            @else
                                <div style="font-size:0.72rem;color:#EA580C;">독립</div>
                            @endif
                        </td>
                        <td class="adm-td-muted">{{ $edition->year ?: '-' }}</td>
                        <td class="adm-td-muted">{{ $edition->race_date?->format('Y.m.d') ?? '-' }}</td>
                        <td class="adm-td-muted">{{ $edition->city ?? '-' }}</td>
                        <td><span class="adm-badge adm-badge-blue">{{ $edition->is_domestic ? '국내' : '해외' }}</span></td>
                        <td><span class="adm-badge adm-badge-gray">{{ $edition->status ?? '접수전' }}</span></td>
                        <td style="text-align:center;color:#9CA3AF;">{{ $edition->review_count ?? 0 }}</td>
                        <td style="text-align:center;">{{ $edition->weather_fetched_at ? '✓' : '-' }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.race-editions.edit', $edition) }}" class="adm-link">수정</a>
                            <form method="POST" action="{{ route('admin.race-editions.destroy', $edition) }}" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn-danger" style="margin-left:0.6rem;">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="adm-td-empty">등록된 인스턴스가 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem;">{{ $editions->links() }}</div>
@endsection
