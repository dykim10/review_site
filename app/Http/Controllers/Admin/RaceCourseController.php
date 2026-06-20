<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RaceCourse;
use App\Models\RaceEdition;
use App\Services\RaceCourseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RaceCourseController extends Controller
{
    public function __construct(private RaceCourseService $service) {}

    public function index()
    {
        $courses = RaceCourse::with('raceEdition.race')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.race-courses.index', compact('courses'));
    }

    public function create()
    {
        $editions = RaceEdition::with('race')
            ->where('is_active', true)
            ->orderByDesc('race_date')
            ->get(['id', 'race_id', 'name', 'year', 'race_date']);

        return view('admin.race-courses.create', compact('editions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'race_edition_id' => ['required', 'integer', Rule::exists(RaceEdition::class, 'id')],
            'course_type'     => 'required|in:FULL,HALF,10K',
            'gpx_file'        => 'required|file|mimes:gpx,xml|max:20480',
            'source'          => 'nullable|in:wari-gari,goandrace,official,manual',
            'is_certified'    => 'nullable|boolean',
            'certified_at'    => 'nullable|date',
        ]);

        $edition = RaceEdition::findOrFail($validated['race_edition_id']);

        try {
            $this->service->uploadAndSave(
                $edition,
                $validated['course_type'],
                $request->file('gpx_file'),
                [
                    'source'      => $validated['source'] ?? 'manual',
                    'is_certified' => (bool) ($validated['is_certified'] ?? false),
                    'certified_at' => $validated['certified_at'] ?? null,
                ]
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['gpx_file' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.race-courses.index')
            ->with('success', 'GPX 코스가 등록되었습니다.');
    }

    public function destroy(RaceCourse $raceCourse)
    {
        $this->service->delete($raceCourse);

        return redirect()->route('admin.race-courses.index')
            ->with('success', 'GPX 코스가 삭제되었습니다.');
    }
}
