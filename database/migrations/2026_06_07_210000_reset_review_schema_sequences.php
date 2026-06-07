<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // 직접 삽입(sync 등)으로 시퀀스와 실제 max(id) 불일치 시 발생하는 PK 충돌 방지
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
