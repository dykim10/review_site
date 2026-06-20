<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * B타입: race_date는 edition 전용. WA 연도별 일정은 races.wa_calendar JSONB 캐시.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.races
            ADD COLUMN IF NOT EXISTS wa_calendar JSONB NOT NULL DEFAULT '{}'::jsonb
        ");

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS wa_calendar");
    }
};
