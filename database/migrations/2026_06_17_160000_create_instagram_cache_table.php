<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS review.instagram_cache (
                id              BIGSERIAL PRIMARY KEY,
                race_edition_id BIGINT REFERENCES review.race_editions(id) ON DELETE SET NULL,
                post_id         VARCHAR(100) NOT NULL,
                caption         TEXT,
                media_url       TEXT,
                thumbnail_url   TEXT,
                like_count      INTEGER DEFAULT 0,
                permalink       TEXT,
                posted_at       DATE,
                fetched_at      TIMESTAMPTZ(6),
                created_at      TIMESTAMPTZ(6),
                updated_at      TIMESTAMPTZ(6),
                UNIQUE (post_id)
            )
        SQL);

        try {
            DB::select('SELECT NULL::vector(1)');
            DB::statement('ALTER TABLE review.instagram_cache ADD COLUMN IF NOT EXISTS embedding vector(1536)');
            DB::statement('CREATE INDEX IF NOT EXISTS ig_cache_embedding_hnsw ON review.instagram_cache USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');
        } catch (\Throwable) {
            // pgvector 미활성 — embedding 없이 생성
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS review.ig_cache_embedding_hnsw');
        DB::statement('DROP TABLE IF EXISTS review.instagram_cache');
    }
};
