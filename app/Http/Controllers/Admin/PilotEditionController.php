<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PilotEditionService;
use Illuminate\Http\Request;

class PilotEditionController extends Controller
{
    public function __construct(private PilotEditionService $pilotEditions) {}

    public function preview(Request $request)
    {
        $data = $this->validatedYears($request);

        $rows = $this->pilotEditions->preview(
            $data['years'],
            $request->boolean('fetch_dates', true),
        );

        return back()
            ->with('pilot_preview', $rows)
            ->with('success', sprintf(
                'Pilot edition 미리보기 — %d개 조합 (연도 %s)',
                count($rows),
                implode(', ', $data['years'])
            ));
    }

    public function provision(Request $request)
    {
        $data = $this->validatedYears($request);

        $result = $this->pilotEditions->provision(
            $data['years'],
            $request->boolean('fetch_dates', true),
        );

        $msg = sprintf(
            'Pilot edition 생성 완료 — 신규 %d / 갱신 %d (연도: %s)',
            $result['created'],
            $result['updated'],
            implode(', ', $data['years']),
        );

        return back()
            ->with('pilot_provision', $result['resultRows'])
            ->with('success', $msg);
    }

    public function attachGpx(Request $request)
    {
        $validated = $request->validate([
            'gpx_year' => ['required', 'integer', 'min:2018', 'max:2035'],
        ]);
        $year = (int) $validated['gpx_year'];

        $rows = $this->pilotEditions->attachGpxStub($year);
        $attached = count(array_filter($rows, fn ($r) => ($r['action'] ?? '') === 'attached'));

        return back()
            ->with('pilot_gpx', $rows)
            ->with('success', sprintf('GPX 스텁 — %d건 등록 (%d년)', $attached, $year));
    }

    /** @return array{years: list<int>} */
    private function validatedYears(Request $request): array
    {
        $validated = $request->validate([
            'years'       => ['required', 'array', 'min:1'],
            'years.*'     => ['integer', 'min:2018', 'max:2035'],
            'fetch_dates' => ['sometimes', 'boolean'],
        ]);

        $years = array_values(array_unique(array_map('intval', $validated['years'])));
        sort($years);

        return ['years' => $years];
    }
}
