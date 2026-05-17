<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE review.races ADD COLUMN IF NOT EXISTS ai_race_summary JSONB");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS ai_race_summary");
    }
};
