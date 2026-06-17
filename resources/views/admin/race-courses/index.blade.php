<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>GPX 코스 관리</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.race-editions.index') }}" class="text-sm text-gray-500 hover:underline">← 대회 인스턴스</a>
            <h1 class="text-xl font-bold mt-1">GPX 코스 관리</h1>
        </div>
        <a href="{{ route('admin.race-courses.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + GPX 업로드
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 text-left">
                <tr>
                    <th class="px-4 py-3">대회</th>
                    <th class="px-4 py-3">연도</th>
                    <th class="px-4 py-3">코스</th>
                    <th class="px-4 py-3">출처</th>
                    <th class="px-4 py-3">공인</th>
                    <th class="px-4 py-3">GPX</th>
                    <th class="px-4 py-3">등록일</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">
                            {{ $course->raceEdition?->race?->name ?? $course->raceEdition?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $course->raceEdition?->year }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                                {{ $course->course_type === 'FULL' ? 'bg-blue-100 text-blue-700' :
                                   ($course->course_type === 'HALF' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $course->course_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $course->source ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($course->is_certified)
                                <span class="text-green-600 font-medium">✓ 공인</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($course->gpx_url)
                                <a href="{{ $course->gpx_url }}" target="_blank"
                                   class="text-blue-600 hover:underline text-xs">다운로드</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $course->created_at?->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST"
                                  action="{{ route('admin.race-courses.destroy', $course) }}"
                                  onsubmit="return confirm('삭제하면 S3 파일도 함께 삭제됩니다. 계속하시겠습니까?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-xs">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                            등록된 GPX 코스가 없습니다.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $courses->links() }}</div>

</div>
</body>
</html>
