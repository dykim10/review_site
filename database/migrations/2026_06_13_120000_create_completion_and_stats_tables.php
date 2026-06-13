<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * completion_records: 사용자 개인 완주기록 (이미지 인증 + GPX).
 * race_stats: race_editions 단위 통계 집계 (빅데이터 / AI 레이스 플랜용).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 완주기록
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.completion_records (
                id                      BIGSERIAL PRIMARY KEY,
                race_edition_id         BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE CASCADE,
                user_id                 BIGINT NOT NULL,
                distance_km             NUMERIC(6,2),
                finish_time_seconds     INTEGER,
                official_time_seconds   INTEGER,
                bib_number              VARCHAR(20),
                certificate_image_url   TEXT,
                gpx_url                 TEXT,
                parsed_data             JSONB,
                is_verified             BOOLEAN NOT NULL DEFAULT false,
                created_at              TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at              TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                CONSTRAINT cr_unique_user_edition UNIQUE (race_edition_id, user_id)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS cr_edition_idx  ON review.completion_records (race_edition_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS cr_user_idx     ON review.completion_records (user_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS cr_verified_idx ON review.completion_records (is_verified)");

        // 대회 통계 (edition 단위, 집계 후 저장)
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_stats (
                id                          BIGSERIAL PRIMARY KEY,
                race_edition_id             BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE CASCADE,
                participant_count           INTEGER NOT NULL DEFAULT 0,
                avg_finish_time_seconds     NUMERIC(10,2),
                median_finish_time_seconds  NUMERIC(10,2),
                finish_time_dist            JSONB,
                avg_pace_seconds            NUMERIC(8,2),
                sentiment_score             NUMERIC(4,2),
                review_count                INTEGER NOT NULL DEFAULT 0,
                training_tips               JSONB,
                race_plan                   JSONB,
                last_calculated_at          TIMESTAMPTZ(6),
                created_at                  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                CONSTRAINT rs_unique_edition UNIQUE (race_edition_id)
            )
        ");

        DB::statement("GRANT ALL ON review.completion_records TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.completion_records_id_seq TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON review.race_stats TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.race_stats_id_seq TO anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.race_stats");
        DB::statement("DROP TABLE IF EXISTS review.completion_records");
    }
};
