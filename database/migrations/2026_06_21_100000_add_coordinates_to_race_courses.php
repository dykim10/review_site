<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE review.race_courses
                ADD COLUMN IF NOT EXISTS coordinates JSONB,
                ADD COLUMN IF NOT EXISTS markers JSONB
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE review.race_courses
                DROP COLUMN IF EXISTS coordinates,
                DROP COLUMN IF EXISTS markers
        ');
    }
};
