<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>대회 인스턴스 등록</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.race-editions.index') }}" class="text-sm text-gray-500 hover:underline">← 목록</a>
    <h1 class="text-xl font-bold mt-4 mb-6">대회 인스턴스 등록</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded p-4 mb-4 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.race-editions.store') }}"
          class="bg-white rounded-lg shadow p-6 space-y-4" x-data="{ isDomestic: true }">
        @csrf

        {{-- 원본 races 연결 (선택) --}}
        <div>
            <label class="block text-sm font-medium mb-1">원본 대회 연결
                <span class="text-gray-400 font-normal text-xs">(선택 — 비워두면 독립 인스턴스)</span>
            </label>
            <select name="race_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">연결 안 함 (독립 인스턴스)</option>
                @foreach($races as $race)
                    <option value="{{ $race->id }}" @selected(old('race_id') == $race->id)>
                        {{ $race->name }}{{ $race->latestEdition ? ' ('.$race->latestEdition->year.')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">대회명 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">대회일</label>
                <input type="date" name="race_date" value="{{ old('race_date') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">개최 연도 <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal text-xs">(대회일 없는 경우 직접 입력)</span>
                </label>
                <input type="number" name="year" value="{{ old('year') }}" required
                       min="1990" max="2100" placeholder="예: 2024"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">시작 시간</label>
                <input type="text" name="race_time" value="{{ old('race_time') }}"
                       placeholder="예: 09:00" class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">상태</label>
                <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                    @foreach(['접수전','접수중','접수마감','대회종료'] as $s)
                        <option value="{{ $s }}" @selected(old('status', '접수전') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">장소</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">도시</label>
                <input type="text" name="city" value="{{ old('city') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            {{-- 국내/해외 구분 --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-2">국내/해외 구분</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_domestic" value="1"
                               @checked(old('is_domestic', '1') === '1')
                               x-on:change="isDomestic = true"
                               class="accent-blue-600">
                        <span class="text-sm">국내</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_domestic" value="0"
                               @checked(old('is_domestic') === '0')
                               x-on:change="isDomestic = false"
                               class="accent-purple-600">
                        <span class="text-sm">해외</span>
                    </label>
                </div>
            </div>
            <div class="col-span-2" x-show="!isDomestic">
                <label class="block text-sm font-medium mb-1">국가</label>
                <input type="text" name="country" value="{{ old('country') }}"
                       placeholder="예: 일본, 미국, 독일"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">참가비</label>
                <input type="text" name="entry_fee" value="{{ old('entry_fee') }}"
                       placeholder="예: 50,000원" class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">기상청 지점코드
                    <span class="text-gray-400 font-normal text-xs">(비워두면 자동추론)</span>
                </label>
                <input type="number" name="weather_stn_id" value="{{ old('weather_stn_id') }}"
                       placeholder="예: 108 (서울)"
                       class="w-full border rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">서울 108 / 인천 112 / 수원 119 / 대전 133 / 광주 156 / 부산 159 / 제주 184</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">접수 시작일</label>
                <input type="date" name="reg_start" value="{{ old('reg_start') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">접수 종료일</label>
                <input type="date" name="reg_end" value="{{ old('reg_end') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.race-editions.index') }}"
               class="px-4 py-2 text-sm border rounded hover:bg-gray-50">취소</a>
            <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">등록</button>
        </div>
    </form>
</div>

<script>
// Alpine.js가 없으면 is_domestic 토글 수동 처리
document.querySelectorAll('input[name="is_domestic"]').forEach(el => {
    el.addEventListener('change', () => {
        const countryRow = document.querySelector('[x-show="!isDomestic"]');
        if (countryRow) {
            countryRow.style.display = el.value === '0' ? '' : 'none';
        }
    });
});
// 초기 상태
(function() {
    const checked = document.querySelector('input[name="is_domestic"]:checked');
    const countryRow = document.querySelector('[x-show="!isDomestic"]');
    if (countryRow && checked && checked.value === '1') {
        countryRow.style.display = 'none';
    }
})();
</script>
</body>
</html>
