<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceCourse;
use App\Models\RaceEdition;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RacePlanService
{
    public function generate(
        RaceEdition $edition,
        int $userId,
        string $courseType,
        string $goalTime,
        string $trainingStatus = 'normal',
        ?float $recentLongKm = null,
        ?string $recent10kTime = null,
    ): array {
        try {
            $response = Http::timeout(120)
                ->post(config('services.core_api.url') . '/api/race-plan/generate', [
                    'race_edition_id' => $edition->id,
                    'user_id'         => $userId,
                    'course_type'     => $courseType,
                    'goal_time'       => $goalTime,
                    'training_status' => $trainingStatus,
                    'recent_long_km'  => $recentLongKm,
                    'recent_10k_time' => $recent10kTime,
                    'live'            => app()->environment('production'),
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('[RacePlan] CORE API 오류', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('레이스 플랜 생성에 실패했습니다. (' . $response->status() . ')');
        } catch (ConnectionException $e) {
            Log::error('[RacePlan] CORE API 연결 오류: ' . $e->getMessage());
            throw new \RuntimeException('CORE API에 연결할 수 없습니다.');
        }
    }

    public function hasOfficialGpx(RaceEdition $edition, string $courseType): bool
    {
        return RaceCourse::where('race_edition_id', $edition->id)
            ->where('course_type', $courseType)
            ->whereNotNull('gpx_url')
            ->exists();
    }

    public function availableEditions(Race $race): \Illuminate\Database\Eloquent\Collection
    {
        return $race->editions()->orderByDesc('year')->get(['id', 'year', 'race_date', 'name', 'status']);
    }

    public function courseTypesFor(Race $race): array
    {
        $distances = $race->distances ?? [];
        $map       = [];
        foreach ($distances as $d) {
            $d = trim($d);
            if (in_array($d, ['풀', '풀마라톤', '42', '42.195km', 'FULL', 'Full'])) {
                $map['FULL'] = '풀마라톤 (42.195km)';
            } elseif (in_array($d, ['하프', '하프마라톤', '21', '21km', 'HALF', 'Half'])) {
                $map['HALF'] = '하프마라톤 (21km)';
            } elseif (in_array($d, ['10K', '10km', '10k'])) {
                $map['10K'] = '10K (10km)';
            }
        }
        if (empty($map)) {
            $map = ['FULL' => '풀마라톤 (42.195km)', 'HALF' => '하프마라톤 (21km)', '10K' => '10K (10km)'];
        }

        return $map;
    }
}
