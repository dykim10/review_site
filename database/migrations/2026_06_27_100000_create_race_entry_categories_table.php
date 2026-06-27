<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('review.race_entry_categories')) {
            DB::statement("
                CREATE TABLE review.race_entry_categories (
                    id               BIGSERIAL PRIMARY KEY,
                    race_edition_id  BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE CASCADE,
                    name             VARCHAR(100) NOT NULL,
                    distance_km      NUMERIC(6,3) NOT NULL,
                    entry_fee        INTEGER NOT NULL CHECK (entry_fee >= 0),
                    sort_order       SMALLINT NOT NULL DEFAULT 0,
                    created_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                    updated_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now()
                )
            ");

            DB::statement('CREATE INDEX rec_edition_idx ON review.race_entry_categories (race_edition_id)');

            DB::statement('GRANT ALL ON review.race_entry_categories TO anon, authenticated, service_role');
            DB::statement('GRANT ALL ON SEQUENCE review.race_entry_categories_id_seq TO anon, authenticated, service_role');
        }

        // 기존 entry_fee → 대표 종목 1줄 이전 (멱등)
        DB::statement("
            INSERT INTO review.race_entry_categories (race_edition_id, name, distance_km, entry_fee, sort_order, created_at, updated_at)
            SELECT
                re.id,
                '기본',
                42.195,
                CAST(re.entry_fee AS INTEGER),
                0,
                now(),
                now()
            FROM review.race_editions re
            WHERE re.entry_fee IS NOT NULL
              AND TRIM(re.entry_fee) ~ '^[0-9]+$'
              AND NOT EXISTS (
                  SELECT 1 FROM review.race_entry_categories c WHERE c.race_edition_id = re.id
              )
        ");

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS review.race_entry_categories');
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }
};
