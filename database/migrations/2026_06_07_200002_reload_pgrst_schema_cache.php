<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgREST 스키마 캐시 강제 갱신 — ALTER TABLE 이후 캐시 미반영 문제 해결
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void {}
};
