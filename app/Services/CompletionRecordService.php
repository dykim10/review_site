<?php

namespace App\Services;

use App\Models\CompletionRecord;
use App\Models\RaceEdition;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompletionRecordService
{
    public function alreadyRecorded(User $user, RaceEdition $edition): bool
    {
        return CompletionRecord::where('user_id', $user->id)
            ->where('race_edition_id', $edition->id)
            ->exists();
    }

    public function getUserRecord(User $user, RaceEdition $edition): ?CompletionRecord
    {
        return CompletionRecord::where('user_id', $user->id)
            ->where('race_edition_id', $edition->id)
            ->first();
    }

    public function create(array $data, User $user, RaceEdition $edition, ?UploadedFile $certFile = null): CompletionRecord
    {
        $certUrl = $certFile ? $this->uploadCertificate($certFile) : null;

        return CompletionRecord::create([
            'race_edition_id'       => $edition->id,
            'user_id'               => $user->id,
            'distance_km'           => $data['distance_km'] ?? null,
            'finish_time_seconds'   => $this->parseTime($data['finish_time'] ?? null),
            'official_time_seconds' => $this->parseTime($data['official_time'] ?? null),
            'bib_number'            => $data['bib_number'] ?? null,
            'certificate_image_url' => $certUrl,
            'is_verified'           => false,
        ]);
    }

    public function delete(CompletionRecord $record): void
    {
        if ($record->certificate_image_url) {
            try {
                Http::timeout(10)->delete(
                    config('services.core_api.url') . '/api/s3/image',
                    ['url' => $record->certificate_image_url]
                );
            } catch (ConnectionException $e) {
                Log::warning('완주 인증서 S3 삭제 실패: ' . $e->getMessage());
            }
        }
        $record->delete();
    }

    /** "H:MM:SS" 또는 "M:SS" 문자열 → 초 변환 */
    public function parseTime(?string $time): ?int
    {
        if (!$time || !preg_match('/^\d+:\d{2}(:\d{2})?$/', trim($time))) {
            return null;
        }
        $parts = array_reverse(explode(':', trim($time)));
        $seconds = 0;
        foreach ($parts as $i => $v) {
            $seconds += (int) $v * (60 ** $i);
        }
        return $seconds > 0 ? $seconds : null;
    }

    private function uploadCertificate(UploadedFile $file): ?string
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.core_api.url') . '/api/photo/resize-webp', [
                    'folder' => 'completions/' . now()->format('Y/m'),
                ]);

            if ($response->successful()) {
                return $response->json('thumbnail_url');
            }
            Log::warning('완주 인증서 업로드 실패', ['status' => $response->status()]);
        } catch (ConnectionException $e) {
            Log::error('CORE API 연결 오류 (완주 인증서): ' . $e->getMessage());
        }
        return null;
    }
}
