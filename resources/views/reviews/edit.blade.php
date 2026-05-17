<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>리뷰 수정</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('races.show', $review->race_id) }}" class="text-sm text-gray-500 hover:underline">← 대회 상세로</a>

        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <h1 class="text-xl font-bold mb-6">리뷰 수정</h1>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reviews.update', $review) }}">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">참가 거리</label>
                    <select name="distance"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('distance') border-red-400 @enderror">
                        <option value="">선택해주세요</option>
                        @foreach(['5K', '10K', '하프', '풀', '기타'] as $d)
                            <option value="{{ $d }}" {{ old('distance', $review->distance) === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('distance')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">평점</label>
                    <div class="flex gap-2" x-data="{ rating: {{ old('rating', $review->rating) }} }">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}"
                                       class="sr-only"
                                       x-on:change="rating = {{ $i }}"
                                       {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                                <span class="text-3xl transition-colors"
                                      x-bind:class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">★</span>
                            </label>
                        @endfor
                    </div>
                    @error('rating')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        리뷰 내용
                        <span class="text-gray-400 font-normal">(최소 10자, 최대 2000자)</span>
                    </label>
                    <textarea name="content" rows="8"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('content') border-red-400 @enderror"
                              placeholder="대회 참가 후기를 자유롭게 작성해주세요.">{{ old('content', $review->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                        수정 완료
                    </button>
                    <a href="{{ route('races.show', $review->race_id) }}"
                       class="bg-gray-100 text-gray-700 px-6 py-2 rounded hover:bg-gray-200 text-sm font-medium">
                        취소
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
