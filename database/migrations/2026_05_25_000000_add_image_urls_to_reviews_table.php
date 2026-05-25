<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.reviews
            ADD COLUMN IF NOT EXISTS image_urls JSONB DEFAULT '[]'::jsonb
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS image_urls");
    }
};
