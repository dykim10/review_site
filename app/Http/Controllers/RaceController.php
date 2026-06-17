<?php

namespace App\Http\Controllers;

use App\Models\CompletionRecord;
use App\Models\InstagramCache;
use App\Models\Race;
use App\Models\YoutubeCache;
use App\Services\RaceService;
use App\Services\ReviewService;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function __construct(
        private RaceService $raceService,
        private ReviewService $reviewService,
        private WeatherService $weatherService,
    ) {}

    public function index(Request $request)
    {
        $races = $this->raceService->getPublicListWithStats($request->only('city', 'status', 'wa_label'));
        return view('races.index', compact('races'));
    }

    public function show(Race $race)
    {
        $reviews         = $this->reviewService->getByRace($race);
        $avgRating       = $this->reviewService->avgRating($race);
        $alreadyReviewed = auth()->check()
            ? $this->reviewService->alreadyReviewed(auth()->user(), $race)
            : false;

        $weather  = $this->weatherService->getForRace($race);
        $editions = $race->editions()
            ->withCount('reviews')
            ->withCount('completionRecords')
            ->orderByDesc('year')
            ->get();

        $myCompletion = null;
        if (auth()->check() && $editions->isNotEmpty()) {
            $myCompletion = CompletionRecord::where('user_id', auth()->id())
                ->whereIn('race_edition_id', $editions->pluck('id'))
                ->with('raceEdition')
                ->first();
        }

        $latestEdition = $editions->first();

        // SNS 캐시 (최신 에디션 기준)
        $youtubeItems   = [];
        $instagramItems = [];
        if ($latestEdition) {
            try {
                $youtubeItems = YoutubeCache::where('race_edition_id', $latestEdition->id)
                    ->orderByDesc('published_at')->limit(6)->get(['video_id','title','url','thumbnail_url','view_count'])->toArray();
                $instagramItems = InstagramCache::where('race_edition_id', $latestEdition->id)
                    ->orderByDesc('posted_at')->limit(9)->get(['post_id','caption','thumbnail_url','permalink','like_count'])->toArray();
            } catch (\Throwable) {
                // SNS 테이블 미생성 시 graceful skip
            }
        }

        return view('races.show', compact(
            'race', 'reviews', 'avgRating', 'alreadyReviewed',
            'weather', 'editions', 'myCompletion', 'latestEdition',
            'youtubeItems', 'instagramItems',
        ));
    }
}
