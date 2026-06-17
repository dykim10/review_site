<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * strava_tokens: Strava OAuth 토큰 암호화 저장.
 *
 * access_token_enc / refresh_token_enc 는 AES-128 암호화 필수.
 * ENCRYPT_KEY (.env) 로 암호화 — 평문 저장 절대 금지.
 * UNIQUE(user_id): 사용자 1인 1개 Strava 계정 연결.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.strava_tokens (
                id                  BIGSERIAL PRIMARY KEY,
                user_id             BIGINT NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
                strava_athlete_id   VARCHAR(50) NOT NULL,
                access_token_enc    TEXT NOT NULL,
                refresh_token_enc   TEXT NOT NULL,
                expires_at          TIMESTAMPTZ NOT NULL,
                created_at          TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at          TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE (user_id)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS st_user_idx    ON review.strava_tokens (user_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS st_athlete_idx ON review.strava_tokens (strava_athlete_id)");

        DB::statement("GRANT ALL ON review.strava_tokens TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.strava_tokens_id_seq TO anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.strava_tokens");
    }
};
