<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대회 인스턴스 관리</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">대회 인스턴스 관리</h1>
            <p class="text-sm text-gray-500 mt-1">races 원본 기반 연도별 개최 인스턴스 (race_editions)</p>
        </div>
        <a href="{{ route('admin.race-editions.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">+ 인스턴스 등록</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4 text-sm">{{ session('success') }}</div>
    @endif

    {{-- 필터 --}}
    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">국내/해외</label>
            <select name="is_domestic" class="border rounded px-3 py-2 text-sm">
                <option value="">전체</option>
                <option value="1" @selected(request('is_domestic') === '1')>국내</option>
                <option value="0" @selected(request('is_domestic') === '0')>해외</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">연도</label>
            <select name="year" class="border rounded px-3 py-2 text-sm">
                <option value="">전체</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}년</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs text-gray-500 mb-1">대회명 검색</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="대회명 입력"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded text-sm">검색</button>
        <a href="{{ route('admin.race-editions.index') }}" class="px-4 py-2 border rounded text-sm hover:bg-gray-50">초기화</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">대회명</th>
                    <th class="text-left px-4 py-3">연도</th>
                    <th class="text-left px-4 py-3">대회일</th>
                    <th class="text-left px-4 py-3">도시</th>
                    <th class="text-left px-4 py-3">구분</th>
                    <th class="text-left px-4 py-3">상태</th>
                    <th class="text-center px-4 py-3">리뷰</th>
                    <th class="text-center px-4 py-3">날씨</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($editions as $edition)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $edition->name }}</div>
                            @if($edition->race)
                                <div class="text-xs text-gray-400">races #{{ $edition->race_id }}</div>
                            @else
                                <div class="text-xs text-orange-400">독립 인스턴스</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $edition->year ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $edition->race_date?->format('Y.m.d') ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $edition->city ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($edition->is_domestic)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">국내</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700">해외</span>
                                <span class="text-xs text-gray-400">{{ $edition->country }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs
                                @if($edition->status === '접수중') bg-green-100 text-green-700
                                @elseif($edition->status === '대회종료') bg-gray-100 text-gray-500
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $edition->status ?? '접수전' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $edition->reviews_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($edition->weather_stn_id)
                                <span class="text-green-500 text-xs">✓ {{ $edition->weather_stn_id }}</span>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.race-editions.edit', $edition) }}"
                               class="text-blue-600 hover:underline">수정</a>
                            <form method="POST"
                                  action="{{ route('admin.race-editions.destroy', $edition) }}"
                                  class="inline"
                                  onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">등록된 대회 인스턴스가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $editions->links() }}</div>

</div>
</body>
</html>
