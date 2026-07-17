<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRaceEditionRequest;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Models\WeatherStation;
use App\Services\RaceEditionService;
use App\Services\RaceService;
use App\Services\WeatherService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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
            $term = '%'.addcslashes(request('q'), '%_\\').'%';
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
        $weatherStations = WeatherStation::optionsForSelect();
        $clonePrefill = session('clone_prefill', []);

        return view('admin.race-editions.create', compact('races', 'weatherStations', 'clonePrefill'));
    }

    public function store(StoreRaceEditionRequest $request)
    {
        try {
            $edition = $this->service->create($request->validated());
        } catch (QueryException $e) {
            if ($this->isUniqueRaceYearViolation($e)) {
                return back()
                    ->withErrors(['year' => '해당 대회의 같은 연도 데이터가 이미 있습니다.'])
                    ->withInput();
            }
            throw $e;
        }

        if (! $edition->weather_stn_id && ($edition->location || $edition->city)) {
            $this->weatherService->autoResolveStnForEdition($edition);
        }

        $this->raceService->syncEntryCategories($edition, $request->input('categories', []) ?? []);

        if ($edition->race_id) {
            $race = Race::find($edition->race_id);
            if ($race) {
                $this->raceService->publishIfHasEditions($race);
            }
        }

        return redirect()->route('admin.race-editions.edit', $edition)
            ->with('success', '연도별 대회가 등록되었습니다.');
    }

    public function edit(RaceEdition $raceEdition)
    {
        $raceEdition->load(['race', 'entryCategories']);

        $races = Race::with('latestEdition')->orderBy('name')->get(['id', 'name']);
        $weatherStations = WeatherStation::optionsForSelect();
        $clonePrefill = session('clone_prefill', []);

        $siblingEditions = $raceEdition->race_id
            ? RaceEdition::where('race_id', $raceEdition->race_id)->orderByDesc('year')->get(['id', 'year', 'race_date'])
            : collect();

        return view('admin.race-editions.edit', [
            'edition'          => $raceEdition,
            'race'             => $raceEdition->race,
            'races'            => $races,
            'siblingEditions'  => $siblingEditions,
            'weatherStations'  => $weatherStations,
            'clonePrefill'     => $clonePrefill,
        ]);
    }

    public function update(StoreRaceEditionRequest $request, RaceEdition $raceEdition)
    {
        try {
            $this->raceService->updateEdition(
                $raceEdition,
                $request->validated(),
                (string) ($request->input('distances_raw') ?? ''),
                $request->input('categories', []) ?? [],
            );
        } catch (QueryException $e) {
            if ($this->isUniqueRaceYearViolation($e)) {
                return back()
                    ->withErrors(['year' => '해당 대회의 같은 연도 데이터가 이미 있습니다.'])
                    ->withInput();
            }
            throw $e;
        }

        return redirect()->route('admin.race-editions.edit', $raceEdition)
            ->with('success', '대회 정보가 저장되었습니다.');
    }

    public function destroy(RaceEdition $raceEdition)
    {
        $this->service->delete($raceEdition);

        return redirect()->route('admin.race-editions.index')
            ->with('success', '연도별 대회가 삭제되었습니다.');
    }

    /**
     * 다음 연도 복제 — 존재하면 edit 프리필, 없으면 create 프리필 (저장 전까지 DB 미변경).
     */
    public function cloneNext(RaceEdition $raceEdition)
    {
        return $this->cloneAdjacent($raceEdition, +1);
    }

    /**
     * 이전 연도 복제 — 과거 기록 추가용. 존재하면 edit 프리필, 없으면 create 프리필.
     */
    public function clonePrevious(RaceEdition $raceEdition)
    {
        return $this->cloneAdjacent($raceEdition, -1);
    }

    /**
     * @param  int  $yearDelta  +1 다음 연도 / -1 이전 연도
     */
    private function cloneAdjacent(RaceEdition $raceEdition, int $yearDelta)
    {
        if (! $raceEdition->race_id) {
            return redirect()->route('admin.race-editions.index')
                ->with('error', '대회(마스터)가 연결되지 않은 연도별 대회는 복제할 수 없습니다.');
        }

        if (! $raceEdition->year) {
            return redirect()->route('admin.race-editions.index')
                ->with('error', '연도가 없는 연도별 대회는 복제할 수 없습니다.');
        }

        $targetYear = (int) $raceEdition->year + $yearDelta;
        if ($targetYear < 1990 || $targetYear > 2100) {
            return redirect()->route('admin.race-editions.index')
                ->with('error', "복제 대상 연도({$targetYear})가 유효하지 않습니다.");
        }

        $directionLabel = $yearDelta > 0 ? '다음' : '이전';
        $status = $yearDelta > 0 ? 'upcoming' : 'ended';

        $prefill = [
            'race_id'        => $raceEdition->race_id,
            'name'           => $raceEdition->name,
            'year'           => $targetYear,
            'race_time'      => $raceEdition->race_time,
            'location'       => $raceEdition->location,
            'city'           => $raceEdition->city,
            'is_domestic'    => $raceEdition->is_domestic ? '1' : '0',
            'country'        => $raceEdition->country,
            'entry_fee'      => $raceEdition->entry_fee,
            'weather_stn_id' => $raceEdition->weather_stn_id,
            'status'         => $status,
            'categories'     => $raceEdition->entryCategories()
                ->get(['name', 'distance_km', 'entry_fee'])
                ->map(fn ($c) => [
                    'name'        => $c->name,
                    'distance_km' => (string) $c->distance_km,
                    'entry_fee'   => (string) $c->entry_fee,
                ])
                ->all(),
        ];

        $existing = RaceEdition::where('race_id', $raceEdition->race_id)
            ->where('year', $targetYear)
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.race-editions.edit', $existing)
                ->with('clone_prefill', $prefill)
                ->with('success', "{$targetYear}년 연도별 대회가 이미 있어 수정 화면으로 이동했습니다. {$directionLabel} 연도 기준으로 폼을 채웠으니 확인 후 저장하세요.");
        }

        return redirect()
            ->route('admin.race-editions.create')
            ->withInput($prefill)
            ->with('clone_prefill', $prefill)
            ->with('success', "{$targetYear}년 연도별 대회 등록 화면입니다. {$directionLabel} 연도 값이 채워져 있습니다. 대회일은 새로 입력하세요.");
    }

    /** 관리자 GPX 폼용 — 에디션 종목 → course_type 후보 */
    public function entryCategoriesJson(RaceEdition $raceEdition): JsonResponse
    {
        $categories = $raceEdition->entryCategories()
            ->get(['name', 'distance_km', 'entry_fee']);

        $courseTypes = [];
        foreach ($categories as $cat) {
            $mapped = $this->mapDistanceToCourseType((float) $cat->distance_km);
            if ($mapped && ! isset($courseTypes[$mapped])) {
                $courseTypes[$mapped] = [
                    'course_type' => $mapped,
                    'name'        => $cat->name,
                    'distance_km' => (float) $cat->distance_km,
                ];
            }
        }

        return response()->json([
            'edition_id'  => $raceEdition->id,
            'categories'  => $categories,
            'course_types'=> array_values($courseTypes),
            'empty'       => $categories->isEmpty(),
        ]);
    }

    private function mapDistanceToCourseType(float $km): ?string
    {
        if (abs($km - 42.195) < 0.5 || abs($km - 42) < 0.5) {
            return 'FULL';
        }
        if (abs($km - 21.0975) < 0.5 || abs($km - 21) < 0.5) {
            return 'HALF';
        }
        if (abs($km - 10) < 0.3) {
            return '10K';
        }

        return null;
    }

    private function isUniqueRaceYearViolation(QueryException $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, 'race_editions_race_id_year_uniq')
            || (str_contains($msg, 'unique') && str_contains($msg, 'race_id') && str_contains($msg, 'year'));
    }
}
