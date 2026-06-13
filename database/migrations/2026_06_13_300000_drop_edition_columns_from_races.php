<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cols = [
            'race_date', 'race_time', 'location', 'entry_fee',
            'reg_start', 'reg_end', 'status', 'source', 'source_url',
            'weather_stn_id', 'weather_fetch_attempted_at',
        ];
        foreach ($cols as $col) {
            DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS {$col}");
        }
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS race_date DATE");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS race_time VARCHAR(20)");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS location VARCHAR(200)");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS entry_fee VARCHAR(100)");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS reg_start DATE");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS reg_end DATE");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active'");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS source VARCHAR(50)");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS source_url TEXT");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS weather_stn_id INTEGER");
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS weather_fetch_attempted_at TIMESTAMPTZ(6)");
    }
};
