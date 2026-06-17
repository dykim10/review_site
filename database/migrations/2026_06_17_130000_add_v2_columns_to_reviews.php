<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * reviews v2 컬럼 추가.
 *
 * race_edition_id 는 이미 추가됨 (2026_06_13_130000).
 * 여기서는 레이스 플랜 / Strava 연동에 필요한 나머지 컬럼을 추가한다.
 */
return new class extends Migration
{
    public function up(): void
    {
        $adds = [
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS finish_time       VARCHAR(8)",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS course_type       VARCHAR(10)",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS source            VARCHAR(10)  DEFAULT 'manual'",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS parsed_watch_data JSONB",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS strava_activity_id VARCHAR(50)",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS gpx_url           TEXT",
            "ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS is_certified      BOOLEAN NOT NULL DEFAULT false",
        ];

        foreach ($adds as $sql) {
            DB::statement($sql);
        }

        DB::statement("CREATE INDEX IF NOT EXISTS reviews_strava_idx ON review.reviews (strava_activity_id)");

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        $drops = [
            'finish_time', 'course_type', 'source',
            'parsed_watch_data', 'strava_activity_id', 'gpx_url', 'is_certified',
        ];
        foreach ($drops as $col) {
            DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS {$col}");
        }
    }
};
