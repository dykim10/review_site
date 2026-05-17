<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.reviews
            ADD COLUMN IF NOT EXISTS ai_summary TEXT,
            ADD COLUMN IF NOT EXISTS sentiment VARCHAR(10)
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS ai_summary");
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS sentiment");
    }
};
