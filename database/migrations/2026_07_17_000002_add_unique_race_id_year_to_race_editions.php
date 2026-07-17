<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dupes = DB::select("
            SELECT race_id, year, COUNT(*) c
            FROM review.race_editions
            WHERE race_id IS NOT NULL
            GROUP BY race_id, year HAVING COUNT(*) > 1
        ");
        if (! empty($dupes)) {
            throw new \RuntimeException(
                'race_editions (race_id, year) 중복 — 수동 정리 후 재실행: '.json_encode($dupes)
            );
        }

        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS race_editions_race_id_year_uniq
            ON review.race_editions (race_id, year)
            WHERE race_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS review.race_editions_race_id_year_uniq');
    }
};
