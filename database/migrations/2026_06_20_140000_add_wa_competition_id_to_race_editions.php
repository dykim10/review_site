<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * WA Label Road Races sync — 연도별 World Athletics competition ID.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.race_editions
            ADD COLUMN IF NOT EXISTS wa_competition_id INTEGER NULL
        ");

        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS race_editions_wa_competition_id_unique
            ON review.race_editions (wa_competition_id)
            WHERE wa_competition_id IS NOT NULL
        ");

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS review.race_editions_wa_competition_id_unique");
        DB::statement("ALTER TABLE review.race_editions DROP COLUMN IF EXISTS wa_competition_id");
    }
};
