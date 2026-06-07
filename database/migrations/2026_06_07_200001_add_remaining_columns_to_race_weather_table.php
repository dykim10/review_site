<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS weather_condition VARCHAR(30)");
        DB::statement("ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS created_at TIMESTAMPTZ(6) DEFAULT now()");
        DB::statement("ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS updated_at TIMESTAMPTZ(6) DEFAULT now()");
    }

    public function down(): void
    {
        foreach (['weather_condition', 'created_at', 'updated_at'] as $col) {
            DB::statement("ALTER TABLE review.race_weather DROP COLUMN IF EXISTS {$col}");
        }
    }
};
