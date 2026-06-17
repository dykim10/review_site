<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Models\RaceEdition;
use App\Services\RacePlanService;
use Illuminate\Http\Request;

class RacePlanController extends Controller
{
    public function __construct(private RacePlanService $racePlanService) {}

    public function create(Race $race)
    {
        $editions    = $this->racePlanService->availableEditions($race);
        $courseTypes = $this->racePlanService->courseTypesFor($race);
        return view('race-plan.create', compact('race', 'editions', 'courseTypes'));
    }

    public function generate(Request $request, Race $race)
    {
        $validated = $request->validate([
            'race_edition_id' => 'required|integer|exists:review.race_editions,id',
            'course_type'     => 'required|in:FULL,HALF,10K',
            'goal_h'          => 'required|integer|min:0|max:9',
            'goal_m'          => 'required|integer|min:0|max:59',
            'goal_s'          => 'required|integer|min:0|max:59',
            'training_status' => 'required|in:best,good,normal,poor',
            'recent_long_km'  => 'nullable|numeric|min:1|max:200',
            'recent_10k_h'    => 'nullable|integer|min:0|max:9',
            'recent_10k_m'    => 'nullable|integer|min:0|max:59',
            'recent_10k_s'    => 'nullable|integer|min:0|max:59',
        ]);

        $goalTime = sprintf('%d:%02d:%02d',
            $validated['goal_h'],
            $validated['goal_m'],
            $validated['goal_s'],
        );

        $recent10kTime = null;
        if (!empty($validated['recent_10k_m'])) {
            $recent10kTime = sprintf('%d:%02d:%02d',
                $validated['recent_10k_h'] ?? 0,
                $validated['recent_10k_m'],
                $validated['recent_10k_s'] ?? 0,
            );
        }

        $edition = RaceEdition::findOrFail($validated['race_edition_id']);

        try {
            $plan = $this->racePlanService->generate(
                edition:        $edition,
                courseType:     $validated['course_type'],
                goalTime:       $goalTime,
                trainingStatus: $validated['training_status'],
                recentLongKm:   $validated['recent_long_km'] ?? null,
                recent10kTime:  $recent10kTime,
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return view('race-plan.result', compact('race', 'edition', 'plan'));
    }
}
