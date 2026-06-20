<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Models\RaceEdition;
use App\Models\RacePlan;
use App\Services\CoreApiClient;
use App\Services\RacePlanService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RacePlanController extends Controller
{
    public function __construct(
        private RacePlanService $racePlanService,
        private CoreApiClient $coreApi,
    ) {}

    public function index(RaceEdition $edition)
    {
        $plans = RacePlan::where('user_id', auth()->id())
            ->where('race_edition_id', $edition->id)
            ->orderByDesc('created_at')
            ->get();

        return view('race-plan.index', compact('edition', 'plans'));
    }

    public function show(RacePlan $plan)
    {
        abort_unless($plan->user_id === auth()->id(), 403);

        return view('race-plan.show', compact('plan'));
    }

    public function create(Race $race)
    {
        $editions    = $this->racePlanService->availableEditions($race);
        $courseTypes = $this->racePlanService->courseTypesFor($race);
        $runningLogs = auth()->check()
            ? $this->coreApi->getUserRunningLogs(auth()->id())
            : [];

        return view('race-plan.create', compact('race', 'editions', 'courseTypes', 'runningLogs'));
    }

    public function generate(Request $request, Race $race)
    {
        $validated = $request->validate([
            'race_edition_id' => ['required', 'integer', Rule::exists(RaceEdition::class, 'id')],
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
        if (! empty($validated['recent_10k_m'])) {
            $recent10kTime = sprintf('%d:%02d:%02d',
                $validated['recent_10k_h'] ?? 0,
                $validated['recent_10k_m'],
                $validated['recent_10k_s'] ?? 0,
            );
        }

        $edition = RaceEdition::findOrFail($validated['race_edition_id']);

        if (! $this->racePlanService->hasOfficialGpx($edition, $validated['course_type'])) {
            return back()->with('error', '공식 GPX 코스가 준비되지 않았습니다.')->withInput();
        }

        try {
            $plan = $this->racePlanService->generate(
                edition:        $edition,
                userId:         auth()->id(),
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
