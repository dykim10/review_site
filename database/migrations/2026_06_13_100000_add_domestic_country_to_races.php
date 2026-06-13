<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.races
            ADD COLUMN IF NOT EXISTS is_domestic BOOLEAN NOT NULL DEFAULT true,
            ADD COLUMN IF NOT EXISTS country     VARCHAR(50) NOT NULL DEFAULT '대한민국'
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS races_is_domestic_idx ON review.races (is_domestic)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS is_domestic");
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS country");
    }
};
