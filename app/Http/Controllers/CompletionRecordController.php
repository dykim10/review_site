<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompletionRequest;
use App\Models\CompletionRecord;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Services\CompletionRecordService;

class CompletionRecordController extends Controller
{
    public function __construct(private CompletionRecordService $service) {}

    public function store(StoreCompletionRequest $request, Race $race)
    {
        $edition = RaceEdition::findOrFail($request->race_edition_id);

        abort_if($edition->race_id !== $race->id, 422, '해당 대회의 회차가 아닙니다.');

        if ($this->service->alreadyRecorded($request->user(), $edition)) {
            return back()->with('error', '이미 이 회차에 완주 기록이 등록되어 있습니다.');
        }

        $this->service->create(
            $request->validated(),
            $request->user(),
            $edition,
            $request->hasFile('certificate') ? $request->file('certificate') : null,
        );

        return back()->with('success', '완주 기록이 등록되었습니다.');
    }

    public function destroy(CompletionRecord $record)
    {
        abort_if($record->user_id !== auth()->id(), 403);
        $this->service->delete($record);
        return back()->with('success', '완주 기록이 삭제되었습니다.');
    }
}
