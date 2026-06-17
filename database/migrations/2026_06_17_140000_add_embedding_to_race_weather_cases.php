<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 로컬 테스트 DB에 pgvector가 없을 경우 graceful skip — 운영 DB에서만 실행됨
        try {
            DB::statement('SELECT NULL::vector(1)');
        } catch (\Throwable) {
            \Illuminate\Support\Facades\Log::warning(
                '[Phase 5] pgvector 미활성화 — embedding 관련 DDL을 건너뜁니다. ' .
                '운영 Supabase Dashboard → Database → Extensions → vector 활성화 후 EC2에서 migrate를 실행하세요.'
            );
            return;
        }

        // embedding 컬럼 추가
        DB::statement('ALTER TABLE review.race_weather_cases ADD COLUMN IF NOT EXISTS embedding vector(1536)');

        // HNSW 코사인 거리 인덱스
        DB::statement(
            'CREATE INDEX IF NOT EXISTS rwc_embedding_hnsw
             ON review.race_weather_cases
             USING hnsw (embedding vector_cosine_ops)
             WITH (m = 16, ef_construction = 64)'
        );

        // 유사 케이스 검색 함수 (public 스키마 — Supabase RPC 기본 노출 대상)
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION public.match_race_weather_cases(
                query_embedding vector(1536),
                match_threshold float DEFAULT 0.5,
                match_count     int   DEFAULT 5
            )
            RETURNS TABLE (
                id                  bigint,
                race_edition_id     bigint,
                review_id           bigint,
                temperature         float,
                humidity            integer,
                wind_speed          float,
                finish_time_seconds integer,
                pace_splits         jsonb,
                heart_rate_splits   jsonb,
                similarity          float
            )
            LANGUAGE sql STABLE
            AS $func$
                SELECT
                    id, race_edition_id, review_id,
                    temperature, humidity, wind_speed,
                    finish_time_seconds, pace_splits, heart_rate_splits,
                    1 - (embedding <=> query_embedding) AS similarity
                FROM review.race_weather_cases
                WHERE embedding IS NOT NULL
                  AND 1 - (embedding <=> query_embedding) > match_threshold
                ORDER BY embedding <=> query_embedding
                LIMIT match_count;
            $func$
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS public.match_race_weather_cases(vector(1536), float, int)');
        DB::statement('DROP INDEX IF EXISTS review.rwc_embedding_hnsw');
        DB::statement('ALTER TABLE review.race_weather_cases DROP COLUMN IF EXISTS embedding');
    }
};
