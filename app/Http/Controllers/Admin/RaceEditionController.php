<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRaceEditionRequest;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Services\RaceEditionService;
use App\Services\RaceService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;

class RaceEditionController extends Controller
{
    public function __construct(
        private RaceEditionService $service,
        private RaceService $raceService,
        private WeatherService $weatherService,
    ) {}

    public function index()
    {
        $query = RaceEdition::with('race')
            ->withCount('reviews');

        if (request('is_domestic') !== null && request('is_domestic') !== '') {
            $query->where('is_domestic', (bool) request('is_domestic'));
        }
        if (request('year')) {
            $query->where('year', request('year'));
        }
        if (request('q')) {
            $term = '%' . addcslashes(request('q'), '%_\\') . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'ilike', $term)
                    ->orWhere('city', 'ilike', $term)
                    ->orWhere('location', 'ilike', $term);
            });
        }

        $editions = $query->orderByDesc('race_date')->paginate(20)->withQueryString();

        $years = DB::table('review.race_editions')
            ->select(DB::raw('DISTINCT year'))
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.race-editions.index', compact('editions', 'years'));
    }

    public function create()
    {
        $races = Race::with('latestEdition')->orderBy('name')->get(['id', 'name']);
        return view('admin.race-editions.create', compact('races'));
    }

    public function store(StoreRaceEditionRequest $request)
    {
        $edition = $this->service->create($request->validated());

        if (!$edition->weather_stn_id && ($edition->location || $edition->city)) {
            $this->weatherService->autoResolveStnForEdition($edition);
        }

        $this->raceService->syncEntryCategories($edition, $request->input('categories', []) ?? []);

        return redirect()->route('admin.race-editions.edit', $edition)
            ->with('success', '대회 인스턴스가 등록되었습니다.');
    }

    public function edit(RaceEdition $raceEdition)
    {
        $raceEdition->load(['race', 'entryCategories']);

        $races = Race::with('latestEdition')->orderBy('name')->get(['id', 'name']);

        $siblingEditions = $raceEdition->race_id
            ? RaceEdition::where('race_id', $raceEdition->race_id)->orderByDesc('year')->get(['id', 'year', 'race_date'])
            : collect();

        return view('admin.race-editions.edit', [
            'edition'         => $raceEdition,
            'race'            => $raceEdition->race,
            'races'           => $races,
            'siblingEditions' => $siblingEditions,
        ]);
    }

    public function update(StoreRaceEditionRequest $request, RaceEdition $raceEdition)
    {
        $this->raceService->updateEdition(
            $raceEdition,
            $request->validated(),
            (string) ($request->input('distances_raw') ?? ''),
            $request->input('categories', []) ?? [],
        );

        return redirect()->route('admin.race-editions.edit', $raceEdition)
            ->with('success', '대회 정보가 저장되었습니다.');
    }

    public function destroy(RaceEdition $raceEdition)
    {
        $this->service->delete($raceEdition);
        return redirect()->route('admin.race-editions.index')
            ->with('success', '대회 인스턴스가 삭제되었습니다.');
    }
}
