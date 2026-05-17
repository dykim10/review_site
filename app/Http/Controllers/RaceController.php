<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Services\RaceService;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function __construct(private RaceService $raceService) {}

    public function index(Request $request)
    {
        $races = $this->raceService->getPublicList($request->only('city', 'status'));
        return view('races.index', compact('races'));
    }

    public function show(Race $race)
    {
        return view('races.show', compact('race'));
    }
}
