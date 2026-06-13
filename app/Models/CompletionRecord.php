<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletionRecord extends Model
{
    protected $table = 'review.completion_records';

    protected $fillable = [
        'race_edition_id', 'user_id',
        'distance_km', 'finish_time_seconds', 'official_time_seconds',
        'bib_number', 'certificate_image_url', 'gpx_url',
        'parsed_data', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'distance_km'             => 'float',
            'finish_time_seconds'     => 'integer',
            'official_time_seconds'   => 'integer',
            'parsed_data'             => 'array',
            'is_verified'             => 'boolean',
        ];
    }

    // ─── Relations ────────────────────────────────────────────

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ────────────────────────────────────────────

    /** 완주 시간을 H:MM:SS 형식으로 반환 */
    public function getFinishTimeFormattedAttribute(): ?string
    {
        return $this->formatSeconds($this->finish_time_seconds);
    }

    public function getOfficialTimeFormattedAttribute(): ?string
    {
        return $this->formatSeconds($this->official_time_seconds);
    }

    private function formatSeconds(?int $seconds): ?string
    {
        if ($seconds === null) return null;
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        return $h > 0
            ? sprintf('%d:%02d:%02d', $h, $m, $s)
            : sprintf('%d:%02d', $m, $s);
    }
}
