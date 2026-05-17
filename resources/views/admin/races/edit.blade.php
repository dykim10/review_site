<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대회 수정</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.races.index') }}" class="text-sm text-gray-500 hover:underline">← 관리자 목록</a>
        <h1 class="text-xl font-bold mt-4 mb-6">대회 수정</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded p-4 mb-4 text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.races.update', $race) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">대회명 <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $race->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">대회일 <span class="text-red-500">*</span></label>
                    <input type="date" name="race_date" value="{{ old('race_date', $race->race_date?->format('Y-m-d')) }}" required class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">대회 시작시간</label>
                    <input type="text" name="race_time" value="{{ old('race_time', $race->race_time) }}" placeholder="예: 09:00" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">장소</label>
                    <input type="text" name="location" value="{{ old('location', $race->location) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">도시</label>
                    <input type="text" name="city" value="{{ old('city', $race->city) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">주최</label>
                    <input type="text" name="organizer" value="{{ old('organizer', $race->organizer) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">참가비 (원)</label>
                    <input type="number" name="entry_fee" value="{{ old('entry_fee', $race->entry_fee) }}" min="0" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">접수 시작일</label>
                    <input type="date" name="reg_start" value="{{ old('reg_start', $race->reg_start?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">접수 종료일</label>
                    <input type="date" name="reg_end" value="{{ old('reg_end', $race->reg_end?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">상태</label>
                    <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                        @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $race->status) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">거리 (쉼표로 구분)</label>
                    <input type="text" name="distances_raw" value="{{ old('distances_raw', $race->distances ? implode(',', $race->distances) : '') }}" placeholder="5K,10K,하프,풀" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">공식 홈페이지</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $race->website_url) }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.races.index') }}" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">취소</a>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">저장</button>
            </div>
        </form>
    </div>
</body>
</html>
