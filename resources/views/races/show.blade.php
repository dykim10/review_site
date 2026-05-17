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
            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'crew_admin')
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.races.edit', $race) }}" class="text-sm text-blue-600 hover:underline">수정</a>
                <form method="POST" action="{{ route('admin.races.destroy', $race) }}" onsubmit="return confirm('삭제하시겠습니까?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:underline">삭제</button>
                </form>
            </div>
            @endif
        @endauth

        {{-- AI 종합 요약 섹션 --}}
        @if($race->ai_race_summary)
            @php $aiSummary = $race->ai_race_summary; @endphp
            <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🤖</span>
                    <h2 class="text-base font-bold text-blue-800">AI 참가 후기 종합 분석</h2>
                    <span class="text-xs text-blue-400 ml-auto">최근 {{ $reviews->total() }}건 기반</span>
                </div>

                <p class="text-sm text-gray-700 leading-relaxed mb-4">{{ $aiSummary['summary'] ?? '' }}</p>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @if(!empty($aiSummary['positives']))
                        <div class="bg-white rounded-lg p-3 border border-green-100">
                            <p class="text-xs font-semibold text-green-600 mb-2">👍 참가자들이 좋아한 점</p>
                            <ul class="space-y-1">
                                @foreach($aiSummary['positives'] as $point)
                                    <li class="text-xs text-gray-700">· {{ $point }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($aiSummary['negatives']))
                        <div class="bg-white rounded-lg p-3 border border-orange-100">
                            <p class="text-xs font-semibold text-orange-500 mb-2">💬 아쉬웠던 점</p>
                            <ul class="space-y-1">
                                @foreach($aiSummary['negatives'] as $point)
                                    <li class="text-xs text-gray-700">· {{ $point }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                @if(!empty($aiSummary['keywords']))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($aiSummary['keywords'] as $keyword)
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full"># {{ $keyword }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- 리뷰 섹션 --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">
                    참가 후기
                    @if($avgRating)
                        <span class="text-yellow-500 text-base font-normal ml-2">★ {{ $avgRating }}</span>
                    @endif
                    <span class="text-gray-400 text-base font-normal">({{ $reviews->total() }}건)</span>
                </h2>

                @auth
                    @if(!$alreadyReviewed)
                        <a href="{{ route('reviews.create', $race) }}"
                           class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
                            리뷰 작성
                        </a>
                    @else
                        <span class="text-sm text-gray-400">이미 리뷰를 작성하셨습니다.</span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">로그인 후 리뷰 작성</a>
                @endauth
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">{{ session('error') }}</div>
            @endif

            @forelse($reviews as $review)
                <div class="bg-white rounded-lg shadow p-5 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-gray-800">{{ $review->user->name }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('Y.m.d') }}</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $review->distance }}</span>
                        </div>
                        <span class="text-yellow-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>

                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->content }}</p>

                    @if($review->ai_summary)
                        <div class="mt-3 p-3 bg-blue-50 rounded text-xs text-blue-700 border border-blue-100">
                            <span class="font-semibold">AI 요약</span>
                            @if($review->sentiment === 'positive')
                                <span class="ml-1 text-green-600">😊 긍정</span>
                            @elseif($review->sentiment === 'negative')
                                <span class="ml-1 text-red-500">😞 부정</span>
                            @else
                                <span class="ml-1 text-gray-500">😐 중립</span>
                            @endif
                            <p class="mt-1 leading-relaxed">{{ $review->ai_summary }}</p>
                        </div>
                    @endif

                    @auth
                        @if($review->user_id === auth()->id())
                            <div class="mt-3 flex gap-3">
                                <a href="{{ route('reviews.edit', $review) }}" class="text-xs text-blue-600 hover:underline">수정</a>
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                                      onsubmit="return confirm('리뷰를 삭제하시겠습니까?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">삭제</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400 text-sm">
                    아직 등록된 리뷰가 없습니다. 첫 번째 리뷰를 작성해보세요!
                </div>
            @endforelse

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</body>
</html>
