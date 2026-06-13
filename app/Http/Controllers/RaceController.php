<?php

namespace App\Http\Controllers;

use App\Models\CompletionRecord;
use App\Models\Race;
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

        return view('races.show', compact(
            'race', 'reviews', 'avgRating', 'alreadyReviewed',
            'weather', 'editions', 'myCompletion',
        ));
    }
}
