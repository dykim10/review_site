<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // review.reviews 는 이미 존재 — 인덱스 및 유니크 제약 추가
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS reviews_user_race_unique
            ON review.reviews (user_id, race_id)
        ");
        DB::statement("CREATE INDEX IF NOT EXISTS reviews_race_id_idx ON review.reviews (race_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS reviews_user_id_idx ON review.reviews (user_id)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS review.reviews_user_race_unique");
        DB::statement("DROP INDEX IF EXISTS review.reviews_race_id_idx");
        DB::statement("DROP INDEX IF EXISTS review.reviews_user_id_idx");
    }
};
