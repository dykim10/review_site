<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대회 목록</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">대회 목록</h1>
            @auth
                <a href="{{ route('admin.races.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ 대회 등록</a>
            @endauth
        </div>

        {{-- 필터 --}}
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="city" value="{{ request('city') }}" placeholder="도시명 검색" class="border rounded px-3 py-2 text-sm">
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">전체 상태</option>
                @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded text-sm">검색</button>
        </form>

        {{-- 목록 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($races as $race)
                <a href="{{ route('races.show', $race) }}" class="block bg-white rounded-lg shadow p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <h2 class="font-semibold text-lg">{{ $race->name }}</h2>
                        <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">{{ $race->status ?? '접수전' }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ $race->race_date?->format('Y.m.d') }} · {{ $race->city ?? $race->location }}</p>
                    @if($race->distances)
                        <p class="text-xs text-gray-400 mt-1">{{ implode(', ', $race->distances) }}</p>
                    @endif
                </a>
            @empty
                <p class="text-gray-400 col-span-2">등록된 대회가 없습니다.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $races->links() }}</div>
    </div>
</body>
</html>
