<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>{{ $race->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('races.index') }}" class="text-sm text-gray-500 hover:underline">← 목록으로</a>

        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-start">
                <h1 class="text-2xl font-bold">{{ $race->name }}</h1>
                <span class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700">{{ $race->status ?? '접수전' }}</span>
            </div>

            <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-400">대회일</dt>
                    <dd class="font-medium">{{ $race->race_date?->format('Y년 m월 d일') }} {{ $race->race_time }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">장소</dt>
                    <dd class="font-medium">{{ $race->location }} {{ $race->city ? "({$race->city})" : '' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">주최</dt>
                    <dd class="font-medium">{{ $race->organizer ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">참가비</dt>
                    <dd class="font-medium">{{ $race->entry_fee ? number_format($race->entry_fee).'원' : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">접수기간</dt>
                    <dd class="font-medium">
                        {{ $race->reg_start?->format('Y.m.d') ?? '-' }} ~ {{ $race->reg_end?->format('Y.m.d') ?? '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400">거리</dt>
                    <dd class="font-medium">{{ $race->distances ? implode(', ', $race->distances) : '-' }}</dd>
                </div>
            </dl>

            @if($race->website_url)
                <a href="{{ $race->website_url }}" target="_blank" class="mt-6 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    공식 홈페이지 →
                </a>
            @endif
        </div>

        @auth
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.races.edit', $race) }}" class="text-sm text-blue-600 hover:underline">수정</a>
                <form method="POST" action="{{ route('admin.races.destroy', $race) }}" onsubmit="return confirm('삭제하시겠습니까?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:underline">삭제</button>
                </form>
            </div>
        @endauth
    </div>
</body>
</html>
