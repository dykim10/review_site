<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoreApiClient
{
    public function getUserRunningLogs(int $userId, int $limit = 20): array
    {
        try {
            $response = Http::timeout(15)
                ->get(config('services.core_api.url') . "/api/running-logs/user/{$userId}", [
                    'limit' => $limit,
                ]);

            if (! $response->successful()) {
                Log::warning('[CoreApi] running-logs 조회 실패', ['status' => $response->status()]);

                return [];
            }

            $data = $response->json();

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            Log::warning('[CoreApi] running-logs 연결 오류: ' . $e->getMessage());

            return [];
        }
    }
}
