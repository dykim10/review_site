<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRaceEditionRequest;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Services\RaceEditionService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;

class RaceEditionController extends Controller
{
    public function __construct(
        private RaceEditionService $service,
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
            $query->where('name', 'ilike', '%' . request('q') . '%');
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

        // 장소 기반 기상청 지점코드 자동 추론
        if (!$edition->weather_stn_id && ($edition->location || $edition->city)) {
            $this->weatherService->autoResolveStnForEdition($edition);
        }

        return redirect()->route('admin.race-editions.index')
            ->with('success', '대회 인스턴스가 등록되었습니다.');
    }

    public function edit(RaceEdition $raceEdition)
    {
        $races = Race::with('latestEdition')->orderBy('name')->get(['id', 'name']);
        return view('admin.race-editions.edit', [
            'edition' => $raceEdition,
            'races'   => $races,
        ]);
    }

    public function update(StoreRaceEditionRequest $request, RaceEdition $raceEdition)
    {
        $locationChanged =
            ($request->location !== $raceEdition->location) ||
            ($request->city !== $raceEdition->city);

        $validated = $request->validated();

        if ($locationChanged && !$request->weather_stn_id) {
            $validated['weather_stn_id'] = null;
        }

        $this->service->update($raceEdition, $validated);
        $raceEdition->refresh();

        if ($locationChanged && !$raceEdition->weather_stn_id) {
            $this->weatherService->autoResolveStnForEdition($raceEdition);
        }

        return redirect()->route('admin.race-editions.index')
            ->with('success', '대회 인스턴스가 수정되었습니다.');
    }

    public function destroy(RaceEdition $raceEdition)
    {
        $this->service->delete($raceEdition);
        return redirect()->route('admin.race-editions.index')
            ->with('success', '대회 인스턴스가 삭제되었습니다.');
    }
}
