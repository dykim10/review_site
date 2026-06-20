<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS race_weather_edition_unique
            ON review.race_weather (race_edition_id)
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS review.race_weather_edition_unique');
    }
};
