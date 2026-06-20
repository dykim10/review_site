<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaLabelSyncService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    /**
     * World Athletics Label Road Races 시즌 sync (core-api POST /api/races/sync).
     *
     * @return array{year:int,total:int,inserted:int,updated:int,decertified:int,skipped:int}
     */
    public function syncSeason(int $year, bool $translate = false, bool $organiser = false): array
    {
        $query = http_build_query([
            'year'      => $year,
            'translate' => $translate ? 'true' : 'false',
            'organiser' => $organiser ? 'true' : 'false',
        ]);

        $response = Http::timeout(180)
            ->acceptJson()
            ->post("{$this->coreApiUrl}/api/races/sync?{$query}");

        if ($response->failed()) {
            $detail = $response->json('detail') ?? $response->body();
            Log::error('WA Label sync failed', [
                'year'   => $year,
                'status' => $response->status(),
                'detail' => $detail,
            ]);
            throw new \RuntimeException(
                is_string($detail) ? $detail : 'WA Label sync API 오류 (HTTP '.$response->status().')'
            );
        }

        return $response->json();
    }

    /**
     * 크롤 미리보기 — DB 변경 없음 (GET /api/races/crawl-wa-labels).
     *
     * @return array<int, array<string, mixed>>
     */
    public function previewSeason(int $year, bool $organiser = false): array
    {
        $query = http_build_query([
            'year'      => $year,
            'organiser' => $organiser ? 'true' : 'false',
        ]);

        $response = Http::timeout(120)
            ->acceptJson()
            ->get("{$this->coreApiUrl}/api/races/crawl-wa-labels?{$query}");

        if ($response->failed()) {
            $detail = $response->json('detail') ?? $response->body();
            throw new \RuntimeException(
                is_string($detail) ? $detail : 'WA crawl API 오류 (HTTP '.$response->status().')'
            );
        }

        return $response->json();
    }
}
