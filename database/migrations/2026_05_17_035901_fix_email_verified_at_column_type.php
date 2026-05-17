<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // email_verified_at 을 나머지 컬럼과 동일한 TIMESTAMPTZ(6) 으로 통일
        DB::statement("
            ALTER TABLE public.users
            ALTER COLUMN email_verified_at TYPE TIMESTAMPTZ(6)
            USING email_verified_at AT TIME ZONE 'UTC'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE public.users
            ALTER COLUMN email_verified_at TYPE TIMESTAMP(0)
            USING email_verified_at AT TIME ZONE 'UTC'
        ");
    }
};
