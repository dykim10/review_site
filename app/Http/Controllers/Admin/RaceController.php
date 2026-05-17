<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRaceRequest;
use App\Models\Race;
use App\Services\RaceService;

class RaceController extends Controller
{
    public function __construct(private RaceService $raceService) {}

    public function index()
    {
        $races = $this->raceService->getAdminList();
        return view('admin.races.index', compact('races'));
    }

    public function create()
    {
        return view('admin.races.create');
    }

    public function store(StoreRaceRequest $request)
    {
        $this->raceService->create($request->validated(), $request->input('distances_raw', ''));
        return redirect()->route('admin.races.index')->with('success', '대회가 등록되었습니다.');
    }

    public function edit(Race $race)
    {
        return view('admin.races.edit', compact('race'));
    }

    public function update(StoreRaceRequest $request, Race $race)
    {
        $this->raceService->update($race, $request->validated(), $request->input('distances_raw', ''));
        return redirect()->route('admin.races.index')->with('success', '대회 정보가 수정되었습니다.');
    }

    public function destroy(Race $race)
    {
        $this->raceService->delete($race);
        return redirect()->route('admin.races.index')->with('success', '대회가 삭제되었습니다.');
    }
}
