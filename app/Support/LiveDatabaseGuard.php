<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * 로컬에서 DB_TARGET=live 일 때 LIVE Supabase 접근 정책.
 *
 * DB_LIVE_READONLY=true  (기본) — INSERT/UPDATE/DELETE 등 쓰기 SQL 차단
 * DB_LIVE_READONLY=false       — 쓰기 허용 (의도적 테스트 시만)
 */
class LiveDatabaseGuard
{
    public static function boot(string $appLabel): void
    {
        if (! app()->environment('local') || config('database.default') !== 'pgsql_live') {
            return;
        }

        $readOnly = filter_var(env('DB_LIVE_READONLY', 'true'), FILTER_VALIDATE_BOOLEAN);

        if ($readOnly) {
            logger()->warning("[{$appLabel}] DB_TARGET=live · READ-ONLY — LIVE DB 쓰기 차단");

            // DB 세션/캐시는 INSERT 필요 → LIVE 읽기 모드에서는 파일로 전환
            if (config('session.driver') === 'database') {
                config(['session.driver' => 'file']);
            }
            if (config('cache.default') === 'database') {
                config(['cache.default' => 'file']);
            }

            DB::connection('pgsql_live')->beforeExecuting(function (string $query): void {
                if (preg_match('/^\s*(INSERT|UPDATE|DELETE|TRUNCATE|DROP|ALTER|CREATE|GRANT|REVOKE)\b/is', $query)) {
                    throw new RuntimeException(
                        'LIVE DB 쓰기가 차단되었습니다 (DB_LIVE_READONLY=true). '
                        .'쓰기 테스트가 필요하면 DB_LIVE_READONLY=false 로 변경하세요.'
                    );
                }
            });
        } else {
            logger()->warning("[{$appLabel}] DB_TARGET=live · WRITE 허용 — LIVE DB 변경 가능 (주의!)");
        }
    }
}
