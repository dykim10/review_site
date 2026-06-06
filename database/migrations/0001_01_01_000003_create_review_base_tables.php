<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * review 스키마 기반 테이블 복구 마이그레이션.
 * 원래 Supabase 프로젝트 생성 시 존재하던 review 스키마 테이블.
 * migrate:fresh 등으로 삭제된 경우를 대비해 IF NOT EXISTS 로 멱등성 보장.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE SCHEMA IF NOT EXISTS review");

        // races
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.races (
                id              BIGSERIAL PRIMARY KEY,
                name            VARCHAR(200) NOT NULL,
                race_date       DATE,
                race_time       VARCHAR(20),
                location        VARCHAR(200),
                city            VARCHAR(100),
                organizer       VARCHAR(200),
                distances       JSONB,
                entry_fee       VARCHAR(100),
                website_url     TEXT,
                reg_start       DATE,
                reg_end         DATE,
                status          VARCHAR(20) NOT NULL DEFAULT 'active',
                source          VARCHAR(50),
                source_url      TEXT,
                is_active       BOOLEAN NOT NULL DEFAULT true,
                ai_race_summary JSONB,
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // reviews
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.reviews (
                id         BIGSERIAL PRIMARY KEY,
                race_id    BIGINT NOT NULL,
                user_id    BIGINT NOT NULL,
                distance   VARCHAR(20),
                rating     SMALLINT,
                content    TEXT,
                ai_summary TEXT,
                sentiment  VARCHAR(20),
                image_urls JSONB,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // race_weather
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_weather (
                id                BIGSERIAL PRIMARY KEY,
                race_id           BIGINT NOT NULL,
                temperature       NUMERIC(5,2),
                humidity          NUMERIC(5,2),
                wind_speed        NUMERIC(5,2),
                weather_condition VARCHAR(100),
                created_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // Supabase PostgREST 접근 권한
        DB::statement("GRANT USAGE ON SCHEMA review TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON ALL TABLES IN SCHEMA review TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON ALL SEQUENCES IN SCHEMA review TO anon, authenticated, service_role");
        DB::statement("ALTER DEFAULT PRIVILEGES IN SCHEMA review GRANT ALL ON TABLES TO anon, authenticated, service_role");
        DB::statement("ALTER DEFAULT PRIVILEGES IN SCHEMA review GRANT ALL ON SEQUENCES TO anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("DROP SCHEMA IF EXISTS review CASCADE");
    }
};
