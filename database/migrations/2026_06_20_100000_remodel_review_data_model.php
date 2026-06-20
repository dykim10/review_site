<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PLAN-REVIEW-DATA-REMODEL (2026-06-20)
 *
 * - race_editions: is_review_open, status(upcoming/ended), FK RESTRICT
 * - reviews: UNIQUE(user, edition), race_id DROP, certificate columns
 * - completion_records DROP
 * - race_weather: race_id DROP, edition FK RESTRICT
 * - race_plans, edition_feedback CREATE
 * - race_weather_cases: is_certified, source
 * - FK ON DELETE RESTRICT 일괄 (review→cases SET NULL 유지)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── race_editions ────────────────────────────────────────────────
        DB::statement("
            ALTER TABLE review.race_editions
            ADD COLUMN IF NOT EXISTS is_review_open BOOLEAN NOT NULL DEFAULT false
        ");

        DB::statement("
            UPDATE review.race_editions
            SET status = CASE
                WHEN status IN ('upcoming', 'ended') THEN status
                WHEN race_date IS NOT NULL AND race_date < CURRENT_DATE THEN 'ended'
                ELSE 'upcoming'
            END
        ");

        DB::statement("
            UPDATE review.race_editions
            SET is_review_open = true
            WHERE status = 'ended' AND is_review_open = false
        ");

        $this->replaceFk(
            'review.race_editions',
            'race_editions_race_id_fkey',
            'race_id',
            'review.races',
            'id',
            'RESTRICT'
        );

        // ── reviews: completion columns + UNIQUE ───────────────────────────
        DB::statement("ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS certificate_url TEXT");
        DB::statement("ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS verified_at TIMESTAMPTZ(6)");
        DB::statement("ALTER TABLE review.reviews ADD COLUMN IF NOT EXISTS verified_by BIGINT");

        DB::statement("DROP INDEX IF EXISTS review.reviews_user_race_unique");

        // race_edition_id NOT NULL (backfill assumed from prior migration)
        DB::statement("
            UPDATE review.reviews rv
            SET race_edition_id = re.id
            FROM review.race_editions re
            WHERE rv.race_edition_id IS NULL
              AND rv.race_id = re.race_id
        ");

        DB::statement("
            DELETE FROM review.reviews WHERE race_edition_id IS NULL
        ");

        DB::statement("ALTER TABLE review.reviews ALTER COLUMN race_edition_id SET NOT NULL");

        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS reviews_user_edition_unique
            ON review.reviews (user_id, race_edition_id)
        ");

        $this->replaceFk(
            'review.reviews',
            'reviews_race_edition_id_fkey',
            'race_edition_id',
            'review.race_editions',
            'id',
            'RESTRICT'
        );

        DB::statement("DROP INDEX IF EXISTS review.reviews_race_id_idx");
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS race_id");

        // ── completion_records 폐기 ────────────────────────────────────────
        DB::statement("DROP TABLE IF EXISTS review.completion_records CASCADE");

        // ── race_weather: edition only ─────────────────────────────────────
        DB::statement("
            UPDATE review.race_weather rw
            SET race_edition_id = re.id
            FROM review.race_editions re
            WHERE rw.race_edition_id IS NULL
              AND rw.race_id = re.race_id
        ");

        DB::statement("DELETE FROM review.race_weather WHERE race_edition_id IS NULL");
        DB::statement("ALTER TABLE review.race_weather ALTER COLUMN race_edition_id SET NOT NULL");

        $this->replaceFk(
            'review.race_weather',
            'race_weather_race_edition_id_fkey',
            'race_edition_id',
            'review.race_editions',
            'id',
            'RESTRICT'
        );

        DB::statement("ALTER TABLE review.race_weather DROP COLUMN IF EXISTS race_id");

        // ── race_plans (신규) ──────────────────────────────────────────────
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.race_plans (
                id                BIGSERIAL PRIMARY KEY,
                user_id           BIGINT NOT NULL,
                race_edition_id   BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE RESTRICT,
                input             JSONB NOT NULL DEFAULT '{}',
                plan_json         JSONB NOT NULL DEFAULT '{}',
                created_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
        DB::statement("
            CREATE INDEX IF NOT EXISTS race_plans_user_edition_idx
            ON review.race_plans (user_id, race_edition_id, created_at DESC)
        ");
        DB::statement("GRANT ALL ON review.race_plans TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.race_plans_id_seq TO anon, authenticated, service_role");

        // ── edition_feedback (신규) ──────────────────────────────────────
        DB::statement("
            CREATE TABLE IF NOT EXISTS review.edition_feedback (
                id                BIGSERIAL PRIMARY KEY,
                user_id           BIGINT NOT NULL,
                race_edition_id   BIGINT NOT NULL REFERENCES review.race_editions(id) ON DELETE RESTRICT,
                content           TEXT NOT NULL,
                category          VARCHAR(50),
                created_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
        DB::statement("
            CREATE INDEX IF NOT EXISTS edition_feedback_edition_idx
            ON review.edition_feedback (race_edition_id)
        ");
        DB::statement("GRANT ALL ON review.edition_feedback TO anon, authenticated, service_role");
        DB::statement("GRANT ALL ON SEQUENCE review.edition_feedback_id_seq TO anon, authenticated, service_role");

        // ── race_weather_cases ─────────────────────────────────────────────
        if ($this->tableExists('review.race_weather_cases')) {
            DB::statement("
                ALTER TABLE review.race_weather_cases
                ADD COLUMN IF NOT EXISTS is_certified BOOLEAN NOT NULL DEFAULT false
            ");
            DB::statement("
                ALTER TABLE review.race_weather_cases
                ADD COLUMN IF NOT EXISTS source VARCHAR(20)
            ");

            $this->replaceFk(
                'review.race_weather_cases',
                'race_weather_cases_race_edition_id_fkey',
                'race_edition_id',
                'review.race_editions',
                'id',
                'RESTRICT'
            );
            $this->replaceFk(
                'review.race_weather_cases',
                'race_weather_cases_review_id_fkey',
                'review_id',
                'review.reviews',
                'id',
                'SET NULL'
            );
        }

        // ── 기타 edition FK → RESTRICT (테이블 있을 때만) ─────────────────
        if ($this->tableExists('review.race_courses')) {
            $this->replaceFk(
                'review.race_courses',
                'race_courses_race_edition_id_fkey',
                'race_edition_id',
                'review.race_editions',
                'id',
                'RESTRICT'
            );
        }
        if ($this->tableExists('review.youtube_cache')) {
            $this->replaceFk(
                'review.youtube_cache',
                'youtube_cache_race_edition_id_fkey',
                'race_edition_id',
                'review.race_editions',
                'id',
                'RESTRICT'
            );
        }
        if ($this->tableExists('review.instagram_cache')) {
            $this->replaceFk(
                'review.instagram_cache',
                'instagram_cache_race_edition_id_fkey',
                'race_edition_id',
                'review.race_editions',
                'id',
                'RESTRICT'
            );
        }

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS review.edition_feedback");
        DB::statement("DROP TABLE IF EXISTS review.race_plans");

        DB::statement("ALTER TABLE review.race_weather_cases DROP COLUMN IF EXISTS is_certified");
        DB::statement("ALTER TABLE review.race_weather_cases DROP COLUMN IF EXISTS source");

        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS certificate_url");
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS verified_at");
        DB::statement("ALTER TABLE review.reviews DROP COLUMN IF EXISTS verified_by");
        DB::statement("DROP INDEX IF EXISTS review.reviews_user_edition_unique");
        DB::statement("ALTER TABLE review.race_editions DROP COLUMN IF EXISTS is_review_open");
    }

    private function tableExists(string $qualifiedName): bool
    {
        [$schema, $table] = explode('.', $qualifiedName, 2);
        $row = DB::selectOne("
            SELECT 1 FROM information_schema.tables
            WHERE table_schema = ? AND table_name = ?
        ", [$schema, $table]);

        return $row !== null;
    }

    private function replaceFk(
        string $table,
        string $constraintName,
        string $column,
        string $refTable,
        string $refColumn,
        string $onDelete,
    ): void {
        DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$constraintName}");

        // PostgreSQL may auto-name constraints differently — drop any FK on column
        DB::statement("
            DO \$\$
            DECLARE r RECORD;
            BEGIN
                FOR r IN (
                    SELECT c.conname
                    FROM pg_constraint c
                    JOIN pg_class t ON t.oid = c.conrelid
                    JOIN pg_namespace n ON n.oid = t.relnamespace
                    WHERE n.nspname || '.' || t.relname = '{$table}'
                      AND c.contype = 'f'
                      AND EXISTS (
                          SELECT 1 FROM unnest(c.conkey) AS k(attnum)
                          JOIN pg_attribute a ON a.attrelid = c.conrelid AND a.attnum = k.attnum
                          WHERE a.attname = '{$column}'
                      )
                ) LOOP
                    EXECUTE format('ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS %I', r.conname);
                END LOOP;
            END \$\$;
        ");

        DB::statement("
            ALTER TABLE {$table}
            ADD CONSTRAINT {$constraintName}
            FOREIGN KEY ({$column}) REFERENCES {$refTable}({$refColumn})
            ON DELETE {$onDelete}
        ");
    }
};
