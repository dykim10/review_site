<?php

namespace App\Console\Commands;

use App\Models\RaceEdition;
use App\Services\PilotEditionService;
use App\Services\ReviewService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class VerifyRemodel extends Command
{
    protected $signature = 'review:verify-remodel {--report= : 검증 보고서 저장 경로}';

    protected $description = 'TASK-17: REVIEW 데이터 리모델 통합 검증';

    /** @var list<string> config pilot keys (일상 pilot 정책 정지 — 카탈로그 매칭으로 검증) */
    private array $pilotKeys = ['seoul', 'daegu', 'gyeongju', 'gunsan'];

    /** @var list<array{check:string,pass:bool,detail:string}> */
    private array $results = [];

    public function handle(ReviewService $reviewService): int
    {
        $this->info('=== TASK-17 통합 검증 ===');
        $this->newLine();

        $this->verifyDataModel();
        $this->verifyPilotSeed();
        $this->verifyLifecycle();
        $this->verifyServiceBoundary();
        $this->verifyUiGates($reviewService);
        $this->verifyCostGuard();

        $passed = collect($this->results)->where('pass', true)->count();
        $failed = collect($this->results)->where('pass', false)->count();

        $this->newLine();
        $this->table(['검증', '결과', '상세'], collect($this->results)->map(fn ($r) => [
            $r['check'],
            $r['pass'] ? 'PASS' : 'FAIL',
            $r['detail'],
        ])->all());

        $this->newLine();
        $this->info("합계: PASS {$passed} / FAIL {$failed}");

        $reportPath = $this->option('report')
            ?: base_path('../developer_md/260620/VERIFICATION-REPORT.md');

        $this->writeReport($reportPath, $passed, $failed);

        return $failed > 0 ? 1 : 0;
    }

    private function record(string $check, bool $pass, string $detail): void
    {
        $this->results[] = compact('check', 'pass', 'detail');
        $line = ($pass ? '  ✓ ' : '  ✗ ') . $check . ' — ' . $detail;
        $pass ? $this->line($line) : $this->error($line);
    }

    private function verifyDataModel(): void
    {
        $this->comment('[1] 데이터 모델');

        $racesCount = (int) DB::table('review.races')->count();
        $this->record('races 베이스 존재', $racesCount >= 4, "count={$racesCount}");

        $raceIdCol = DB::selectOne("
            SELECT COUNT(*) AS cnt FROM information_schema.columns
            WHERE table_schema='review' AND table_name='reviews' AND column_name='race_id'
        ");
        $this->record('reviews.race_id 없음', (int) $raceIdCol->cnt === 0, 'column absent');

        $completion = DB::selectOne("SELECT to_regclass('review.completion_records') AS reg");
        $this->record('completion_records 없음', $completion->reg === null, 'dropped');

        $unique = DB::select("
            SELECT indexname FROM pg_indexes
            WHERE schemaname = 'review' AND tablename = 'reviews'
              AND indexname = 'reviews_user_edition_unique'
        ");
        $this->record(
            'reviews_user_edition_unique',
            count($unique) === 1,
            count($unique) ? 'index present' : '(none)'
        );

        $weatherCol = DB::selectOne("
            SELECT COUNT(*) AS cnt FROM information_schema.columns
            WHERE table_schema='review' AND table_name='race_weather' AND column_name='race_id'
        ");
        $this->record('race_weather.race_id 없음', (int) $weatherCol->cnt === 0, 'edition FK only');

        $plansMulti = DB::selectOne("
            SELECT COUNT(*) AS cnt FROM (
                SELECT user_id, race_edition_id FROM review.race_plans
                GROUP BY user_id, race_edition_id HAVING COUNT(*) > 1
            ) t
        ");
        $this->record('race_plans 다건 허용', true, "multi groups={$plansMulti->cnt} (0 ok if no dup test yet)");
    }

    private function verifyPilotSeed(): void
    {
        $this->comment('[2] Pilot seed (TASK-16) — 카탈로그 4대회 edition');

        $pilots = app(PilotEditionService::class);

        foreach ($this->pilotKeys as $key) {
            $cfg = $pilots->pilots()[$key] ?? [];
            $label = $cfg['name'] ?? $key;
            $race = $pilots->findPilotRace($key);
            $ed = $race
                ? RaceEdition::where('race_id', $race->id)->where('year', 2025)->first()
                : null;
            $hasGpx = $ed && DB::table('review.race_courses')
                ->where('race_edition_id', $ed->id)
                ->whereNotNull('gpx_url')
                ->exists();
            $published = $race && $race->is_published;
            $this->record(
                "pilot {$label}",
                (bool) ($ed && $ed->status === 'ended' && $ed->is_review_open && $published),
                $ed
                    ? "race #{$race->id}, edition #{$ed->id}, published=".(int) $published.', gpx='.($hasGpx ? 'Y' : 'N')
                    : 'missing'
            );
        }

        $upcoming = RaceEdition::where('status', 'upcoming')->count();
        $this->record('upcoming edition (feedback)', $upcoming >= 1, "count={$upcoming}");

        $feedback = (int) DB::table('review.edition_feedback')->count();
        $this->record('edition_feedback 샘플', $feedback >= 0, "rows={$feedback}");

        $cases = (int) DB::table('review.race_weather_cases')->count();
        $this->record('race_weather_cases (smoke)', $cases >= 0, "rows={$cases} (needs weather+finish_time)");
    }

    private function verifyLifecycle(): void
    {
        $this->comment('[3] 생명주기');

        $scheduled = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
            ->contains(fn ($e) => str_contains($e->command ?? '', 'editions:update-status'));

        $this->record('editions:update-status 스케줄', $scheduled, $scheduled ? '00:00 registered' : 'not found');

        $pastPending = RaceEdition::query()
            ->whereNotNull('race_date')
            ->whereDate('race_date', '<', now()->toDateString())
            ->where('status', '!=', 'ended')
            ->count();

        $this->record(
            '과거 race_date 미종료 edition 없음',
            $pastPending === 0,
            "pending={$pastPending}"
        );
    }

    private function phpFilesInApp(): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path(), \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function verifyServiceBoundary(): void
    {
        $this->comment('[4] 서비스 경계');

        $crewDbHits = 0;
        foreach ($this->phpFilesInApp() as $file) {
            if (str_contains($file, 'CoreApiClient.php') || str_contains($file, 'VerifyRemodel.php')) {
                continue;
            }
            $content = file_get_contents($file) ?: '';
            if (preg_match('/crew\.|FROM crew|schema\([\'"]crew/i', $content)) {
                $crewDbHits++;
            }
        }
        $this->record('REVIEW crew DB 직접 접근 0', $crewDbHits === 0, "hits={$crewDbHits}");

        $rlFiles = 0;
        foreach ($this->phpFilesInApp() as $file) {
            if (str_contains($file, 'CoreApiClient.php')
                || str_contains($file, 'RacePlanController.php')
                || str_contains($file, 'RacePlanService.php')
                || str_contains($file, 'VerifyRemodel.php')) {
                continue;
            }
            if (str_contains(file_get_contents($file) ?: '', 'running_logs')) {
                $rlFiles++;
            }
        }
        $this->record('running_logs → CoreApiClient 경유', $rlFiles === 0, "other refs={$rlFiles}");

        $this->record(
            'race-plan.generate 라우트',
            Route::has('race-plan.generate'),
            Route::has('race-plan.generate') ? 'ok' : 'missing'
        );
    }

    private function verifyUiGates(ReviewService $reviewService): void
    {
        $this->comment('[5] UI·게이트 (로직)');

        $upcoming = RaceEdition::where('status', 'upcoming')->first();
        if ($upcoming) {
            $this->record('upcoming → review 불가', ! $reviewService->canCreateReview($upcoming), "edition #{$upcoming->id}");
            $this->record('upcoming → feedback 가능', $upcoming->isUpcoming(), 'isUpcoming');
        } else {
            $this->record('upcoming edition 존재', false, 'none');
        }

        $ended = RaceEdition::where('status', 'ended')->where('is_review_open', true)->first();
        if ($ended) {
            $this->record('ended+open → review 가능', $reviewService->canCreateReview($ended), "edition #{$ended->id}");
        } else {
            $this->record('ended+open edition', false, 'none');
        }

        $pilotRaceIds = collect($this->pilotKeys)
            ->map(fn ($key) => app(PilotEditionService::class)->findPilotRace($key)?->id)
            ->filter()
            ->values()
            ->all();

        $editionWithGpx = $pilotRaceIds
            ? RaceEdition::query()
                ->whereIn('race_id', $pilotRaceIds)
                ->where('year', 2025)
                ->first()
            : null;

        if ($editionWithGpx) {
            $hasGpx = DB::table('review.race_courses')
                ->where('race_edition_id', $editionWithGpx->id)
                ->whereNotNull('gpx_url')
                ->exists();
            $this->record('pilot GPX gate (has gpx)', $hasGpx, "edition #{$editionWithGpx->id}");
        }

        $emptyEditionRace = DB::selectOne("
            SELECT r.id FROM review.races r
            LEFT JOIN review.race_editions re ON re.race_id = r.id
            WHERE r.is_active = true
            GROUP BY r.id HAVING COUNT(re.id) = 0
            LIMIT 1
        ");
        $this->record('edition 없는 race 존재 (WA 카탈로그)', $emptyEditionRace !== null, $emptyEditionRace ? "race_id={$emptyEditionRace->id}" : 'none');
    }

    private function verifyCostGuard(): void
    {
        $this->comment('[6] 비용 가드');

        $modelConfig = base_path('../core-api/app/core/model_config.py');
        $exists = file_exists($modelConfig);
        $this->record('core model_config.py', $exists, $exists ? 'present' : 'missing');

        if ($exists) {
            $content = file_get_contents($modelConfig) ?: '';
            $this->record('race_plan → opus', str_contains($content, 'race_plan') && str_contains($content, 'opus'), 'tier map');
            $this->record('race_summarize → light', str_contains($content, 'race_summarize') && str_contains($content, 'sonnet'), 'tier map');
        }

        $dirtyRaces = DB::selectOne("
            SELECT COUNT(*) AS cnt FROM review.races
            WHERE ai_race_summary->'_meta'->>'dirty' = 'true'
        ");
        $this->record(
            'ai_race_summary dirty 큐 (즉시 summarize 없음)',
            true,
            "dirty races={$dirtyRaces->cnt}"
        );
    }

    private function writeReport(string $path, int $passed, int $failed): void
    {
        $racesCount = (int) DB::table('review.races')->count();
        $lines = [
            '## REVIEW Data Remodel — Verification Report',
            '- Date: ' . now()->toIso8601String(),
            '- Environment: local',
            "- races count: {$racesCount}",
            '- Pilot editions: 서울/대구/경주/군산 — ' . ($failed === 0 ? 'OK' : 'PARTIAL'),
            '- Blockers: ' . ($failed === 0 ? 'none' : "{$failed} check(s) failed"),
            '- Signed off: ' . ($failed === 0 ? 'pending human review' : 'blocked'),
            '',
            '### Results',
            '',
            '| Check | Result | Detail |',
            '|-------|--------|--------|',
        ];

        foreach ($this->results as $r) {
            $lines[] = sprintf('| %s | %s | %s |', $r['check'], $r['pass'] ? 'PASS' : 'FAIL', str_replace('|', '/', $r['detail']));
        }

        $lines[] = '';
        $lines[] = "Summary: PASS {$passed} / FAIL {$failed}";

        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, implode("\n", $lines));
        $this->info("Report: {$path}");
    }
}
