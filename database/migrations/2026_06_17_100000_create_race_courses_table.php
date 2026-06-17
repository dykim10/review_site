<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_courses (
                id               BIGSERIAL PRIMARY KEY,
                race_edition_id  BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE CASCADE,
                course_type      VARCHAR(10) NOT NULL,
                gpx_url          TEXT,
                elevation_data   JSONB,
                segments         JSONB,
                source           VARCHAR(50),
                is_certified     BOOLEAN NOT NULL DEFAULT false,
                certified_at     DATE,
                created_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE (race_edition_id, course_type)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS rc_edition_idx ON review.race_courses (race_edition_id)");

        DB::statement("GRANT ALL ON review.race_courses TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.race_courses_id_seq TO anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.race_courses");
    }
};
