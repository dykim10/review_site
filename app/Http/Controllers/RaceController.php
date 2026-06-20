<?php

namespace App\Http\Controllers;

use App\Models\EditionFeedback;
use App\Models\InstagramCache;
use App\Models\Race;
use App\Models\RaceCourse;
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
        $filters  = $request->only('is_domestic', 'wa_label', 'year', 'has_review');
        $sections = $this->raceService->getSectionedList($filters);
        $stats    = $this->raceService->getGlobalStats();

        return view('races.index', [
            'upcoming' => $sections['upcoming'],
            'past'     => $sections['past'],
            'years'    => $sections['years'],
            'stats'    => $stats,
            'filters'  => $filters,
        ]);
    }

    public function show(Race $race)
    {
        $reviews   = $this->reviewService->getByRace($race);
        $avgRating = $this->reviewService->avgRating($race);

        $editions = $race->editions()
            ->withCount('reviews')
            ->orderByDesc('year')
            ->get();

        $latestEdition = $editions->first();

        $alreadyReviewed = false;
        $myReview        = null;
        $feedbacks       = collect();
        $hasOfficialGpx  = false;

        if ($latestEdition) {
            $hasOfficialGpx = RaceCourse::where('race_edition_id', $latestEdition->id)
                ->whereNotNull('gpx_url')
                ->exists();

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
        ));
    }
}
