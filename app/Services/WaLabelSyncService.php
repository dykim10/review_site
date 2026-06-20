<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaLabelSyncService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    public static function cacheKey(int $year): string
    {
        return "wa_label_sync:{$year}";
    }

    /** @return array<string, mixed>|null */
    public function getSyncStatus(int $year): ?array
    {
        $status = Cache::get(self::cacheKey($year));

        return is_array($status) ? $status : null;
    }

    public function markRunning(int $year): void
    {
        Cache::put(self::cacheKey($year), [
            'status'     => 'running',
            'started_at' => now()->toIso8601String(),
        ], 7200);
    }

    /** @param array<string, mixed> $result */
    public function markDone(int $year, array $result): void
    {
        $prev = $this->getSyncStatus($year) ?? [];
        Cache::put(self::cacheKey($year), [
            'status'      => 'done',
            'started_at'  => $prev['started_at'] ?? now()->toIso8601String(),
            'finished_at' => now()->toIso8601String(),
            'result'      => $result,
        ], 7200);
    }

    public function markFailed(int $year, string $message): void
    {
        Cache::put(self::cacheKey($year), [
            'status'      => 'failed',
            'finished_at' => now()->toIso8601String(),
            'error'       => $message,
        ], 7200);
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

        $response = Http::timeout(600)
            ->connectTimeout(15)
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

        $response = Http::timeout(180)
            ->connectTimeout(15)
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
