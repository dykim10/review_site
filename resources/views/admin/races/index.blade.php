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
