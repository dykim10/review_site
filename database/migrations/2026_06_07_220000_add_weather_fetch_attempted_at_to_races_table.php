<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.races
            ADD COLUMN IF NOT EXISTS weather_fetch_attempted_at TIMESTAMPTZ(6) NULL
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS weather_fetch_attempted_at");
    }
};
