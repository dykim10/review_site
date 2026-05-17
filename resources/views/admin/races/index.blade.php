<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대회 관리</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">대회 관리</h1>
            <a href="{{ route('admin.races.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">+ 대회 등록</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">대회명</th>
                        <th class="text-left px-4 py-3">대회일</th>
                        <th class="text-left px-4 py-3">도시</th>
                        <th class="text-left px-4 py-3">상태</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($races as $race)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('races.show', $race) }}" class="hover:underline font-medium">{{ $race->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $race->race_date?->format('Y.m.d') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $race->city ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">{{ $race->status ?? '접수전' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.races.edit', $race) }}" class="text-blue-600 hover:underline">수정</a>
                                <form method="POST" action="{{ route('admin.races.destroy', $race) }}" class="inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">삭제</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">등록된 대회가 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $races->links() }}</div>
    </div>
</body>
</html>
