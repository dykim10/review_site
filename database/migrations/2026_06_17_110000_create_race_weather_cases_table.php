<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * race_weather_cases: 날씨 × 기록 RAG 케이스 누적 테이블.
 *
 * embedding (vector) 컬럼은 pgvector 활성화 확인 후 별도 마이그레이션으로 추가한다.
 * Supabase Dashboard → Database → Extensions 탭에서 pgvector 활성화 여부 먼저 확인 필요.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_weather_cases (
                id                   BIGSERIAL PRIMARY KEY,
                race_edition_id      BIGINT REFERENCES review.race_editions(id) ON DELETE SET NULL,
                review_id            BIGINT REFERENCES review.reviews(id) ON DELETE SET NULL,
                temperature          FLOAT,
                humidity             INTEGER,
                wind_speed           FLOAT,
                finish_time_seconds  INTEGER,
                pace_splits          JSONB,
                heart_rate_splits    JSONB,
                created_at           TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at           TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS rwc_edition_idx ON review.race_weather_cases (race_edition_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS rwc_review_idx  ON review.race_weather_cases (review_id)");

        DB::statement("GRANT ALL ON review.race_weather_cases TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.race_weather_cases_id_seq TO anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.race_weather_cases");
    }
};
