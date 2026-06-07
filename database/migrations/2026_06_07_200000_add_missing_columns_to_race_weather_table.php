<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'stn_id'            => "ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS stn_id INTEGER",
            'wind_direction'    => "ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS wind_direction NUMERIC(5,1)",
            'precipitation'     => "ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS precipitation NUMERIC(7,2)",
            'raw_data'          => "ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS raw_data JSONB",
            'fetched_at'        => "ALTER TABLE review.race_weather ADD COLUMN IF NOT EXISTS fetched_at TIMESTAMPTZ(6)",
        ];

        foreach ($columns as $sql) {
            DB::statement($sql);
        }
    }

    public function down(): void
    {
        $drops = ['stn_id', 'wind_direction', 'precipitation', 'raw_data', 'fetched_at'];
        foreach ($drops as $col) {
            DB::statement("ALTER TABLE review.race_weather DROP COLUMN IF EXISTS {$col}");
        }
    }
};
