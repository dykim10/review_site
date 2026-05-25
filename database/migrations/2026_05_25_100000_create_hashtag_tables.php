<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 해시태그 마스터 (review / youtube / apify 등 source 구분)
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.hashtags (
                id           BIGSERIAL PRIMARY KEY,
                name         VARCHAR(100) NOT NULL,
                slug         VARCHAR(100) NOT NULL,
                source       VARCHAR(20)  NOT NULL DEFAULT 'review',
                usage_count  INTEGER      NOT NULL DEFAULT 0,
                created_at   TIMESTAMPTZ(6) DEFAULT NOW(),
                updated_at   TIMESTAMPTZ(6) DEFAULT NOW(),
                CONSTRAINT hashtags_slug_unique UNIQUE (slug)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS hashtags_usage_idx ON review.hashtags (usage_count DESC)");

        // 리뷰 ↔ 해시태그 pivot
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.review_hashtags (
                id          BIGSERIAL PRIMARY KEY,
                review_id   BIGINT NOT NULL REFERENCES review.reviews(id)  ON DELETE CASCADE,
                hashtag_id  BIGINT NOT NULL REFERENCES review.hashtags(id) ON DELETE CASCADE,
                created_at  TIMESTAMPTZ(6) DEFAULT NOW(),
                CONSTRAINT review_hashtags_unique UNIQUE (review_id, hashtag_id)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS rh_review_id_idx   ON review.review_hashtags (review_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS rh_hashtag_id_idx  ON review.review_hashtags (hashtag_id)");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.review_hashtags");
        DB::statement("DROP TABLE IF EXISTS review.hashtags");
    }
};
