<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Race;
use App\Services\RaceService;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'race_date'   => 'required|date',
            'race_time'   => 'nullable|string|max:20',
            'location'    => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'organizer'   => 'nullable|string|max:255',
            'entry_fee'   => 'nullable|integer|min:0',
            'website_url' => 'nullable|url|max:500',
            'reg_start'   => 'nullable|date',
            'reg_end'     => 'nullable|date|after_or_equal:reg_start',
            'status'      => 'nullable|string|in:접수전,접수중,접수마감,대회종료',
        ]);

        $this->raceService->create($validated, $request->input('distances_raw', ''));

        return redirect()->route('admin.races.index')->with('success', '대회가 등록되었습니다.');
    }

    public function edit(Race $race)
    {
        return view('admin.races.edit', compact('race'));
    }

    public function update(Request $request, Race $race)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'race_date'   => 'required|date',
            'race_time'   => 'nullable|string|max:20',
            'location'    => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'organizer'   => 'nullable|string|max:255',
            'entry_fee'   => 'nullable|integer|min:0',
            'website_url' => 'nullable|url|max:500',
            'reg_start'   => 'nullable|date',
            'reg_end'     => 'nullable|date|after_or_equal:reg_start',
            'status'      => 'nullable|string|in:접수전,접수중,접수마감,대회종료',
        ]);

        $this->raceService->update($race, $validated, $request->input('distances_raw', ''));

        return redirect()->route('admin.races.index')->with('success', '대회 정보가 수정되었습니다.');
    }

    public function destroy(Race $race)
    {
        $this->raceService->delete($race);
        return redirect()->route('admin.races.index')->with('success', '대회가 삭제되었습니다.');
    }
}
