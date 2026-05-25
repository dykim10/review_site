<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Services\RaceService;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function __construct(
        private RaceService $raceService,
        private ReviewService $reviewService,
    ) {}

    public function index(Request $request)
    {
        $races = $this->raceService->getPublicListWithStats($request->only('city', 'status'));
        return view('races.index', compact('races'));
    }

    public function show(Race $race)
    {
        $reviews  = $this->reviewService->getByRace($race);
        $avgRating = $this->reviewService->avgRating($race);
        $alreadyReviewed = auth()->check()
            ? $this->reviewService->alreadyReviewed(auth()->user(), $race)
            : false;

        return view('races.show', compact('race', 'reviews', 'avgRating', 'alreadyReviewed'));
    }
}
