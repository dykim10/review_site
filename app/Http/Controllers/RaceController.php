<?php

namespace App\Http\Controllers;

use App\Models\EditionFeedback;
use App\Models\InstagramCache;
use App\Models\Race;
use App\Models\RaceCourse;
use App\Models\RaceEdition;
use App\Models\Review;
use App\Models\YoutubeCache;
use Illuminate\Http\Response;
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
        $filters  = $request->only('is_domestic', 'wa_label', 'year', 'has_review');
        $sections = $this->raceService->getSectionedList($filters);
        $stats    = $this->raceService->getGlobalStats();

        return view('races.index', [
            'upcoming'    => $sections['upcoming'],
            'past'        => $sections['past'],
            'catalogOnly' => $sections['catalogOnly'],
            'years'       => $sections['years'],
            'stats'       => $stats,
            'filters'     => $filters,
        ]);
    }

    public function show(Race $race)
    {
        $editions      = $this->loadEditions($race);
        $latestEdition = $editions->first();

        return $this->renderRaceDetail($race, $editions, $latestEdition);
    }

    /**
     * 연도별 상세 — 목록(지난 대회 리뷰 등)에서 특정 edition을 클릭했을 때,
     * 항상 최신 edition으로만 가는 show()와 달리 그 연도 그대로 보여준다.
     */
    public function showEdition(Race $race, RaceEdition $edition)
    {
        if ($edition->race_id !== $race->id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $editions = $this->loadEditions($race);

        return $this->renderRaceDetail($race, $editions, $edition);
    }

    private function loadEditions(Race $race)
    {
        return $race->editions()
            ->withCount('reviews')
            ->orderByDesc('year')
            ->get();
    }

    private function renderRaceDetail(Race $race, $editions, ?RaceEdition $latestEdition)
    {
        $reviews   = $latestEdition ? $this->reviewService->getByEdition($latestEdition) : Review::whereRaw('1=0')->paginate(10);
        $avgRating = $latestEdition ? $this->reviewService->avgRatingForEdition($latestEdition) : null;

        $alreadyReviewed = false;
        $myReview        = null;
        $feedbacks       = collect();
        $hasOfficialGpx  = false;
        $coursesForMap   = collect();
        $hasCourseMap    = false;

        if ($latestEdition) {
            $hasOfficialGpx = RaceCourse::where('race_edition_id', $latestEdition->id)
                ->whereNotNull('gpx_url')
                ->exists();

            $coursesForMap = RaceCourse::where('race_edition_id', $latestEdition->id)
                ->whereNotNull('coordinates')
                ->orderByRaw("CASE course_type WHEN 'FULL' THEN 1 WHEN 'HALF' THEN 2 WHEN '10K' THEN 3 ELSE 4 END")
                ->get(['course_type', 'coordinates', 'markers']);

            $hasCourseMap = $coursesForMap->isNotEmpty();

            if ($latestEdition->isUpcoming()) {
                $feedbacks = EditionFeedback::where('race_edition_id', $latestEdition->id)
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();
            }
        }

        if (auth()->check() && $latestEdition) {
            $myReview = $this->reviewService->findUserReview(auth()->user(), $latestEdition);
            $alreadyReviewed = $myReview !== null;
        }

        $weather = $latestEdition
            ? $this->weatherService->getForEdition($latestEdition)
            : null;

        $youtubeItems   = [];
        $instagramItems = [];
        if ($latestEdition) {
            try {
                $youtubeItems = YoutubeCache::where('race_edition_id', $latestEdition->id)
                    ->orderByDesc('published_at')->limit(6)->get(['video_id', 'title', 'url', 'thumbnail_url', 'view_count'])->toArray();
                $instagramItems = InstagramCache::where('race_edition_id', $latestEdition->id)
                    ->orderByDesc('posted_at')->limit(9)->get(['post_id', 'caption', 'thumbnail_url', 'permalink', 'like_count'])->toArray();
            } catch (\Throwable) {
                // SNS 테이블 미생성 시 graceful skip
            }
        }

        return view('races.show', compact(
            'race', 'reviews', 'avgRating', 'alreadyReviewed',
            'weather', 'editions', 'myReview', 'latestEdition',
            'youtubeItems', 'instagramItems', 'feedbacks', 'hasOfficialGpx',
            'coursesForMap', 'hasCourseMap',
        ));
    }
}
