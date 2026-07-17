<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('review.races', 'is_published')) {
            Schema::table('review.races', function (Blueprint $table) {
                // 공개 목록·스케줄러·타 서비스 소비 대상. 기본 false = 후보 카탈로그
                $table->boolean('is_published')->default(false)->index();
            });
        }

        // 이미 editions가 있는 마스터만 운영 연속성을 위해 승격
        // 금지: WHERE wa_label IS NOT NULL (실DB에서 거의 전원 NOT NULL → 전원 공개 사고)
        DB::statement("
            UPDATE review.races r
            SET is_published = true
            WHERE EXISTS (
                SELECT 1 FROM review.race_editions e WHERE e.race_id = r.id
            )
        ");

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        // is_published 값 복구 불가 — 컬럼만 drop
        if (Schema::hasColumn('review.races', 'is_published')) {
            Schema::table('review.races', function (Blueprint $table) {
                $table->dropColumn('is_published');
            });
            DB::statement("NOTIFY pgrst, 'reload schema'");
        }
    }
};
