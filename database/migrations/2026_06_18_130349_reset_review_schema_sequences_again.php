<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // sync_review.py / sync_live_to_local.py 등 외부 동기화가 explicit id로 재삽입하면서
    // 2026-06-07 시퀀스 리셋이 다시 깨짐 (race_weather_pkey 충돌 재발). 동일 패턴으로 재정렬.
    public function up(): void
    {
        $tables = ['races', 'reviews', 'race_weather'];
        foreach ($tables as $table) {
            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('review.{$table}', 'id'),
                    COALESCE((SELECT MAX(id) FROM review.{$table}), 1)
                )
            ");
        }
    }

    public function down(): void {}
};
