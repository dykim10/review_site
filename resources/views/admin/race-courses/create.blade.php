<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>GPX 코스 업로드</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.race-courses.index') }}" class="text-sm text-gray-500 hover:underline">← GPX 코스 목록</a>
    <h1 class="text-xl font-bold mt-4 mb-6">GPX 코스 업로드</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded p-4 mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.race-courses.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">
                대회 인스턴스 <span class="text-red-500">*</span>
            </label>
            <select name="race_edition_id" required
                    class="w-full border rounded px-3 py-2 text-sm">
                <option value="">선택하세요</option>
                @foreach($editions as $edition)
                    <option value="{{ $edition->id }}" @selected(old('race_edition_id') == $edition->id)>
                        {{ $edition->race?->name ?? $edition->name }}
                        ({{ $edition->year }}년
                        @if($edition->race_date), {{ \Carbon\Carbon::parse($edition->race_date)->format('m/d') }}@endif)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                코스 타입 <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @foreach(['FULL' => '풀마라톤 (42.195km)', 'HALF' => '하프마라톤 (21km)', '10K' => '10K'] as $val => $label)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="radio" name="course_type" value="{{ $val }}"
                               @checked(old('course_type') === $val)
                               class="accent-blue-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                GPX 파일 <span class="text-red-500">*</span>
                <span class="text-gray-400 font-normal text-xs">.gpx 또는 .xml, 최대 20MB</span>
            </label>
            <input type="file" name="gpx_file" accept=".gpx,.xml" required
                   class="w-full border rounded px-3 py-2 text-sm bg-white">
            <p class="text-xs text-gray-400 mt-1">
                추천 출처: wari-gari.com / goandrace.com / 대회 공식 홈페이지
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">출처</label>
            <select name="source" class="w-full border rounded px-3 py-2 text-sm">
                @foreach(['manual' => '직접 등록', 'wari-gari' => 'wari-gari.com', 'goandrace' => 'goandrace.com', 'official' => '대회 공식'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('source', 'manual') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" id="is_certified" name="is_certified" value="1"
                   @checked(old('is_certified'))
                   class="accent-blue-600 w-4 h-4">
            <label for="is_certified" class="text-sm cursor-pointer">
                세계육상연맹(WA) 공인 코스
            </label>
        </div>

        <div id="certified_at_wrap" class="{{ old('is_certified') ? '' : 'hidden' }}">
            <label class="block text-sm font-medium mb-1">공인 날짜</label>
            <input type="date" name="certified_at" value="{{ old('certified_at') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm font-medium">
                업로드
            </button>
            <a href="{{ route('admin.race-courses.index') }}"
               class="border border-gray-300 text-gray-600 px-5 py-2 rounded text-sm hover:bg-gray-50">
                취소
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('is_certified').addEventListener('change', function () {
    document.getElementById('certified_at_wrap').classList.toggle('hidden', !this.checked);
});
</script>
</body>
</html>
