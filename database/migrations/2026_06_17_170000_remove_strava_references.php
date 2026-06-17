<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Strava 참조 제거 클린업.
 *
 * 이미 이전 마이그레이션에서 strava_tokens 테이블과 strava_activity_id 컬럼이
 * 생성됐을 수 있으므로 DROP IF EXISTS 로 안전하게 제거한다.
 * EC2 처럼 처음부터 없는 환경에서는 no-op.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.strava_tokens CASCADE");
        DB::statement("DROP INDEX IF EXISTS review.reviews_strava_idx");
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS strava_activity_id");
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void {}
};
