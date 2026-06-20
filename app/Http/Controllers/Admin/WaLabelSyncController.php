<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WaLabelSyncService;
use Illuminate\Http\Request;

class WaLabelSyncController extends Controller
{
    public function __construct(private WaLabelSyncService $waLabelSync) {}

    public function sync(Request $request)
    {
        $data = $request->validate([
            'year'      => ['required', 'integer', 'min:2018', 'max:2035'],
            'translate' => ['sometimes', 'boolean'],
            'organiser' => ['sometimes', 'boolean'],
        ]);

        $year      = (int) $data['year'];
        $translate = $request->boolean('translate');
        $organiser = $request->boolean('organiser');

        try {
            $result = $this->waLabelSync->syncSeason($year, $translate, $organiser);
        } catch (\Throwable $e) {
            return back()->with('error', "WA 동기화 실패 ({$year}): ".$e->getMessage());
        }

        $msg = sprintf(
            '%d 시즌 동기화 완료 — 수집 %d건 / 신규 %d / 갱신 %d / 비공인 %d / skip %d',
            $year,
            $result['total'] ?? 0,
            $result['inserted'] ?? 0,
            $result['updated'] ?? 0,
            $result['decertified'] ?? 0,
            $result['skipped'] ?? 0,
        );

        return back()->with('success', $msg);
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'year'      => ['required', 'integer', 'min:2018', 'max:2035'],
            'organiser' => ['sometimes', 'boolean'],
        ]);

        try {
            $rows = $this->waLabelSync->previewSeason((int) $data['year'], $request->boolean('organiser'));
        } catch (\Throwable $e) {
            return back()->with('error', 'WA 목록 조회 실패: '.$e->getMessage());
        }

        return back()->with('success', sprintf(
            '%d 시즌 WA Label Road Races — %d건 (DB 변경 없음, 미리보기만)',
            $data['year'],
            count($rows),
        ));
    }
}
