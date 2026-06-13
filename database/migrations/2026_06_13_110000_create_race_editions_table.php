<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * race_editions: races(마스터)에서 파생된 연도별 개최 인스턴스 테이블.
 *
 * races 테이블에서 날짜/장소/날씨 관련 컬럼을 이동하여 저장한다.
 * 기존 303건 races 데이터를 시드로 1:1 복사한다.
 * race_id가 NULL인 경우 = 1990년~과거 대회처럼 races 원본 없이 직접 등록된 인스턴스.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_editions (
                id                          BIGSERIAL PRIMARY KEY,
                race_id                     BIGINT REFERENCES review.races(id) ON DELETE SET NULL,
                name                        VARCHAR(200) NOT NULL,
                year                        SMALLINT NOT NULL,
                race_date                   DATE,
                race_time                   VARCHAR(20),
                location                    VARCHAR(200),
                city                        VARCHAR(100),
                is_domestic                 BOOLEAN NOT NULL DEFAULT true,
                country                     VARCHAR(50)  NOT NULL DEFAULT '대한민국',
                source                      VARCHAR(50),
                source_url                  TEXT,
                weather_stn_id              INTEGER,
                weather_fetch_attempted_at  TIMESTAMPTZ(6),
                entry_fee                   VARCHAR(100),
                reg_start                   DATE,
                reg_end                     DATE,
                status                      VARCHAR(20) NOT NULL DEFAULT 'active',
                is_active                   BOOLEAN NOT NULL DEFAULT true,
                created_at                  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at                  TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS re_race_id_idx    ON review.race_editions (race_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS re_year_idx        ON review.race_editions (year)");
        DB::statement("CREATE INDEX IF NOT EXISTS re_race_date_idx   ON review.race_editions (race_date)");
        DB::statement("CREATE INDEX IF NOT EXISTS re_is_domestic_idx ON review.race_editions (is_domestic)");

        DB::statement("GRANT ALL ON review.race_editions TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.race_editions_id_seq TO anon, authenticated, service_role");

        // 기존 races 303건 → race_editions 시드 (race_date 없는 건은 year=0으로 처리)
        DB::statement("
            INSERT INTO review.race_editions (
                race_id, name, year,
                race_date, race_time, location, city,
                is_domestic, country,
                source, source_url,
                weather_stn_id, weather_fetch_attempted_at,
                entry_fee, reg_start, reg_end, status, is_active,
                created_at, updated_at
            )
            SELECT
                id,
                name,
                COALESCE(EXTRACT(YEAR FROM race_date)::SMALLINT, 0),
                race_date, race_time, location, city,
                true, '대한민국',
                source, source_url,
                weather_stn_id, weather_fetch_attempted_at,
                entry_fee, reg_start, reg_end, status, is_active,
                created_at, updated_at
            FROM review.races
            ON CONFLICT DO NOTHING
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.race_editions");
    }
};
