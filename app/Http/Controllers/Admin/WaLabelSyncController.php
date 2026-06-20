<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WaLabelSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

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

        $status = $this->waLabelSync->getSyncStatus($year);
        if (in_array($status['status'] ?? null, ['running', 'cancelling'], true)) {
            return back()->with('error', "{$year} 시즌 동기화가 이미 진행 중입니다.");
        }

        $sessionId = $this->waLabelSync->newSessionId();
        $this->waLabelSync->markRunning($year, $sessionId);

        Bus::dispatchAfterResponse(function () use ($year, $translate, $organiser, $sessionId): void {
            set_time_limit(0);

            try {
                $result = app(WaLabelSyncService::class)->syncSeason(
                    $year,
                    $translate,
                    $organiser,
                    $sessionId,
                );

                if (($result['status'] ?? null) === 'cancelled') {
                    app(WaLabelSyncService::class)->markCancelled($year, $result);

                    return;
                }

                app(WaLabelSyncService::class)->markDone($year, $result);
            } catch (\Throwable $e) {
                app(WaLabelSyncService::class)->markFailed($year, $e->getMessage());
                Log::error('WA Label background sync failed', [
                    'year'  => $year,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return back()->with(
            'success',
            "{$year} 시즌 동기화를 시작했습니다. 중지하려면 아래 「중지·롤백」 버튼을 사용하세요."
        );
    }

    public function cancel(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2018', 'max:2035'],
        ]);

        $year   = (int) $data['year'];
        $status = $this->waLabelSync->getSyncStatus($year);

        if (! in_array($status['status'] ?? null, ['running', 'cancelling'], true)) {
            return back()->with('error', "{$year} 시즌에 진행 중인 동기화가 없습니다.");
        }

        $sessionId = $status['session_id'] ?? null;
        if (! $sessionId) {
            return back()->with('error', 'session_id가 없어 중지할 수 없습니다.');
        }

        $this->waLabelSync->markCancelling($year);

        try {
            $this->waLabelSync->requestCancel($sessionId);
        } catch (\Throwable $e) {
            Log::warning('WA cancel request failed', ['year' => $year, 'error' => $e->getMessage()]);
        }

        return back()->with(
            'success',
            "{$year} 시즌 동기화 중지를 요청했습니다. 롤백 완료까지 잠시 후 새로고침하세요."
        );
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
