<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE review.races
                ADD COLUMN IF NOT EXISTS wa_label     VARCHAR(20)   NULL,
                ADD COLUMN IF NOT EXISTS is_certified BOOLEAN       NOT NULL DEFAULT FALSE,
                ADD COLUMN IF NOT EXISTS official_url VARCHAR(500)  NULL
        ");

        // 기존 크롤링 데이터는 미공인으로 표시
        DB::statement("
            UPDATE review.races
            SET is_certified = FALSE
            WHERE is_certified IS DISTINCT FROM FALSE
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS wa_label");
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS is_certified");
        DB::statement("ALTER TABLE review.races DROP COLUMN IF EXISTS official_url");
    }
};
