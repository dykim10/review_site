<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS review.youtube_cache (
                id              BIGSERIAL PRIMARY KEY,
                race_edition_id BIGINT REFERENCES review.race_editions(id) ON DELETE SET NULL,
                video_id        VARCHAR(30)  NOT NULL,
                title           TEXT,
                description     TEXT,
                url             TEXT,
                thumbnail_url   TEXT,
                view_count      INTEGER,
                published_at    DATE,
                fetched_at      TIMESTAMPTZ(6),
                created_at      TIMESTAMPTZ(6),
                updated_at      TIMESTAMPTZ(6),
                UNIQUE (video_id)
            )
        SQL);

        // pgvector 활성화 시에만 embedding 컬럼 추가
        try {
            DB::select('SELECT NULL::vector(1)');
            DB::statement('ALTER TABLE review.youtube_cache ADD COLUMN IF NOT EXISTS embedding vector(1536)');
            DB::statement('CREATE INDEX IF NOT EXISTS yt_cache_embedding_hnsw ON review.youtube_cache USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');
        } catch (\Throwable) {
            // pgvector 미활성 — embedding 컬럼 없이 생성 (EC2 배포 시 자동 추가)
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS review.yt_cache_embedding_hnsw');
        DB::statement('DROP TABLE IF EXISTS review.youtube_cache');
    }
};
