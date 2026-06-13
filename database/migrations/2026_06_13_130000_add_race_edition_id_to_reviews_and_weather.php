<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * reviews: race_edition_id 추가 + 기존 race_id 기반으로 백필.
 * race_weather: race_edition_id 추가 (nullable, 신규 날씨 수집부터 적용).
 */
return new class extends Migration
{
    public function up(): void
    {
        // reviews — race_edition_id 추가
        DB::statement("
            ALTER TABLE review.reviews
            ADD COLUMN IF NOT EXISTS race_edition_id BIGINT
                REFERENCES review.race_editions(id) ON DELETE SET NULL
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS reviews_edition_idx ON review.reviews (race_edition_id)");

        // 기존 reviews 백필 — race_id → race_edition_id 매핑 (1:1 시드 기준)
        DB::statement("
            UPDATE review.reviews rv
            SET race_edition_id = re.id
            FROM review.race_editions re
            WHERE rv.race_id = re.race_id
              AND rv.race_edition_id IS NULL
        ");

        // race_weather — race_edition_id 추가 (기존 race_id 유지)
        DB::statement("
            ALTER TABLE review.race_weather
            ADD COLUMN IF NOT EXISTS race_edition_id BIGINT
                REFERENCES review.race_editions(id) ON DELETE SET NULL
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS rw_edition_idx ON review.race_weather (race_edition_id)");

        // race_weather 백필
        DB::statement("
            UPDATE review.race_weather rw
            SET race_edition_id = re.id
            FROM review.race_editions re
            WHERE rw.race_id = re.race_id
              AND rw.race_edition_id IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.race_weather DROP COLUMN IF EXISTS race_edition_id");
        DB::statement("ALTER TABLE review.reviews     DROP COLUMN IF EXISTS race_edition_id");
    }
};
