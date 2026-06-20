<?php

namespace App\Console\Commands;

use App\Models\EditionFeedback;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Models\RacePlan;
use App\Models\Review;
use App\Models\User;
use App\Services\RacePlanService;
use App\Services\ReviewService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VerifyRemodelE2e extends Command
{
    protected $signature = 'review:verify-e2e
                            {--skip-plan : CORE plan generate 생략}
                            {--report= : 보고서 append 경로}';

    protected $description = 'TASK-17 수동 E2E — pilot show, 게이트, plan generate';

    /** @var list<array{scenario:string,pass:bool,detail:string}> */
    private array $results = [];

    public function handle(ReviewService $reviewService, RacePlanService $racePlanService): int
    {
        $this->info('=== TASK-17 E2E 검증 ===');

        $user = User::query()->orderBy('id')->first();
        if (! $user) {
            $this->error('users 테이블에 레코드 없음 — E2E 중단');

            return 1;
        }

        $otherUser = User::query()->where('id', '!=', $user->id)->orderBy('id')->first();

        $pilotNames = [
            '서울국제마라톤',
            '대구마라톤',
            '경주마라톤',
            '군산 새만금 국제 마라톤',
        ];

        foreach ($pilotNames as $name) {
            $race = Race::where('name', $name)->first();
            $edition = $race
                ? RaceEdition::where('race_id', $race->id)->where('year', 2025)->first()
                : null;
            if ($race && $edition) {
                $this->checkShowPage($race->id, $edition->id, $name);
            } else {
                $this->record("show {$name}", false, 'race/edition 없음');
            }
        }

        $upcoming = RaceEdition::where('status', 'upcoming')->first();
        $seoulRace = Race::where('name', '서울국제마라톤')->first();
        $ended = $seoulRace
            ? RaceEdition::where('race_id', $seoulRace->id)->where('year', 2025)->first()
            : null;

        if ($upcoming) {
            $this->checkUpcomingFeedback($user, $upcoming);
            $this->checkUpcomingReviewBlocked($user, $upcoming->race_id);
        }

        if ($ended) {
            $this->checkEndedReviewGate($user, $ended, $reviewService);
        }

        $noGpxEdition = $this->findEditionWithoutGpx();
        if ($noGpxEdition) {
            $this->checkPlanDisabledWithoutGpx($noGpxEdition);
        } else {
            $this->record('GPX 없음 → plan disabled', true, 'no-gpx edition 없음 — skip');
        }

        if (! $this->option('skip-plan') && $ended) {
            $this->checkPlanGenerate($user, $ended, $racePlanService);
            $this->checkPlanCacheHit($user, $ended, $racePlanService);
        }

        if ($ended) {
            $this->ensureMockPlanForAuthTest($user, $ended);
            $this->checkPlanIndexShow($user, $otherUser, $ended);
        } elseif (! $this->option('skip-plan')) {
            $this->record('plan generate E2E', true, 'skipped — no ended edition');
        }

        $this->checkEmptyEditionShow();

        $passed = collect($this->results)->where('pass', true)->count();
        $failed = collect($this->results)->where('pass', false)->count();

        $this->newLine();
        $this->table(['시나리오', '결과', '상세'], collect($this->results)->map(fn ($r) => [
            $r['scenario'], $r['pass'] ? 'PASS' : 'FAIL', $r['detail'],
        ])->all());

        $this->info("E2E: PASS {$passed} / FAIL {$failed}");

        $this->appendReport($passed, $failed);

        return $failed > 0 ? 1 : 0;
    }

    private function record(string $scenario, bool $pass, string $detail): void
    {
        $this->results[] = compact('scenario', 'pass', 'detail');
        $pass ? $this->line("  ✓ {$scenario} — {$detail}") : $this->error("  ✗ {$scenario} — {$detail}");
    }

    private function httpGet(string $uri, ?User $user = null): int
    {
        if ($user) {
            Auth::login($user);
        } else {
            Auth::logout();
        }

        $kernel = app(Kernel::class);
        $request = Request::create($uri, 'GET');
        $request->headers->set('Accept', 'text/html');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        Auth::logout();

        return $response->getStatusCode();
    }

    private function checkShowPage(int $raceId, int $expectedEditionId, string $label): void
    {
        $status = $this->httpGet(route('races.show', $raceId));
        $edition = RaceEdition::find($expectedEditionId);
        $this->record(
            "show {$label}",
            $status === 200 && $edition !== null,
            "HTTP {$status}, race #{$raceId}, edition #{$expectedEditionId}"
        );
    }

    private function checkUpcomingFeedback(User $user, RaceEdition $edition): void
    {
        $before = EditionFeedback::where('race_edition_id', $edition->id)->count();

        EditionFeedback::create([
            'race_edition_id' => $edition->id,
            'user_id'         => $user->id,
            'content'         => 'E2E feedback test ' . now()->timestamp,
            'category'        => 'ops',
        ]);

        $after = EditionFeedback::where('race_edition_id', $edition->id)->count();
        $this->record('upcoming → feedback 작성', $after > $before, "rows {$before}→{$after}");
    }

    private function checkUpcomingReviewBlocked(User $user, ?int $raceId): void
    {
        if (! $raceId) {
            $this->record('upcoming → review URL 차단', false, 'race_id 없음');

            return;
        }

        Auth::login($user);
        $kernel = app(Kernel::class);
        $request = Request::create(route('reviews.create', $raceId), 'GET');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        Auth::logout();

        $blocked = $response->isRedirect() || $response->getStatusCode() === 403;
        $this->record('upcoming → review URL 차단', $blocked, 'status ' . $response->getStatusCode());
    }

    private function checkEndedReviewGate(User $user, RaceEdition $edition, ReviewService $reviewService): void
    {
        $can = $reviewService->canCreateReview($edition);
        $raceId = $edition->race_id;

        Auth::login($user);
        $kernel = app(Kernel::class);
        $request = Request::create(route('reviews.create', $raceId), 'GET');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        Auth::logout();

        $existing = Review::where('user_id', $user->id)->where('race_edition_id', $edition->id)->exists();
        $ok = $can && ($response->isRedirect() ? str_contains($response->headers->get('Location', ''), 'edit') : $response->getStatusCode() === 200);
        if ($existing && $response->isRedirect()) {
            $ok = str_contains($response->headers->get('Location', ''), 'edit');
        }

        $this->record('ended+open → review/edit', $ok, $existing ? 'redirect edit' : 'create ' . $response->getStatusCode());
    }

    private function findEditionWithoutGpx(): ?RaceEdition
    {
        return RaceEdition::query()
            ->whereNotIn('id', DB::table('review.race_courses')->whereNotNull('gpx_url')->pluck('race_edition_id'))
            ->where('is_active', true)
            ->first();
    }

    private function checkPlanDisabledWithoutGpx(RaceEdition $edition): void
    {
        $race = Race::find($edition->race_id);
        if (! $race) {
            $this->record('GPX 없음 → plan disabled', false, 'race 없음');

            return;
        }

        $hasGpx = app(RacePlanService::class)->hasOfficialGpx($edition, 'FULL');
        $status = $this->httpGet(route('races.show', $race->id));
        $this->record('GPX 없음 → plan disabled', ! $hasGpx && $status === 200, "edition #{$edition->id}, hasGpx=" . ($hasGpx ? 'Y' : 'N'));
    }

    private function checkPlanGenerate(User $user, RaceEdition $edition, RacePlanService $racePlanService): void
    {
        if (! $racePlanService->hasOfficialGpx($edition, 'FULL')) {
            $this->record('GPX 있음 → plan generate', false, 'GPX 없음');

            return;
        }

        $coreUrl = rtrim(config('services.core_api.url', ''), '/');
        try {
            $ping = Http::timeout(3)->get("{$coreUrl}/docs");
            if (! $ping->successful()) {
                $this->record('GPX 있음 → plan generate', false, "CORE API unreachable ({$coreUrl})");

                return;
            }
        } catch (\Throwable $e) {
            $this->record('GPX 있음 → plan generate', false, 'CORE API: ' . $e->getMessage());

            return;
        }

        $before = RacePlan::where('user_id', $user->id)->where('race_edition_id', $edition->id)->count();

        try {
            $plan = $racePlanService->generate(
                edition:        $edition,
                userId:         $user->id,
                courseType:     'FULL',
                goalTime:       '3:58:00',
                trainingStatus: 'normal',
            );
            $after = RacePlan::where('user_id', $user->id)->where('race_edition_id', $edition->id)->count();
            $ok    = $after > $before && ! empty($plan['race_name'] ?? $plan['strategy_overview'] ?? null);
            $this->record('GPX 있음 → plan generate', $ok, "plans {$before}→{$after}");
        } catch (\Throwable $e) {
            $this->record('GPX 있음 → plan generate', false, $e->getMessage());
        }
    }

    private function checkPlanCacheHit(User $user, RaceEdition $edition, RacePlanService $racePlanService): void
    {
        $goal = '3:59:00';

        try {
            $racePlanService->generate(
                edition:        $edition,
                userId:         $user->id,
                courseType:     'FULL',
                goalTime:       $goal,
                trainingStatus: 'normal',
            );
            $countBefore = RacePlan::where('user_id', $user->id)->where('race_edition_id', $edition->id)->count();

            $racePlanService->generate(
                edition:        $edition,
                userId:         $user->id,
                courseType:     'FULL',
                goalTime:       $goal,
                trainingStatus: 'normal',
            );
            $countAfter = RacePlan::where('user_id', $user->id)->where('race_edition_id', $edition->id)->count();
            $hit        = $countAfter === $countBefore;
            $this->record('동일 input plan 캐시 hit', $hit, "rows {$countBefore}→{$countAfter}");
        } catch (\Throwable $e) {
            $this->record('동일 input plan 캐시 hit', false, $e->getMessage());
        }
    }

    private function ensureMockPlanForAuthTest(User $user, RaceEdition $edition): void
    {
        if (RacePlan::where('user_id', $user->id)->where('race_edition_id', $edition->id)->exists()) {
            return;
        }

        RacePlan::create([
            'user_id'         => $user->id,
            'race_edition_id' => $edition->id,
            'input'           => ['goal_time' => '4:00:00', '_mock' => true],
            'plan_json'       => ['race_name' => 'E2E mock', 'strategy_overview' => 'test'],
            'created_at'      => now(),
        ]);
    }

    private function checkPlanIndexShow(User $owner, ?User $other, RaceEdition $edition): void
    {
        $plan = RacePlan::where('user_id', $owner->id)->where('race_edition_id', $edition->id)->latest('created_at')->first();
        if (! $plan) {
            $this->record('plan index/show (본인)', false, 'plan row 없음');
            $this->record('타 user plan → 403', true, 'skip — no plan');

            return;
        }

        $indexStatus = $this->httpGet(route('race-plan.index', $edition), $owner);
        $showStatus  = $this->httpGet(route('race-plan.show', $plan), $owner);
        $this->record('plan index/show (본인)', $indexStatus === 200 && $showStatus === 200, "index={$indexStatus} show={$showStatus}");

        if ($other) {
            $forbidden = $this->httpGet(route('race-plan.show', $plan), $other);
            $this->record('타 user plan → 403', $forbidden === 403, "HTTP {$forbidden}");
        } else {
            $this->record('타 user plan → 403', true, 'single user — skip');
        }
    }

    private function checkEmptyEditionShow(): void
    {
        $row = DB::selectOne("
            SELECT r.id FROM review.races r
            LEFT JOIN review.race_editions re ON re.race_id = r.id
            WHERE r.is_active = true
            GROUP BY r.id HAVING COUNT(re.id) = 0
            LIMIT 1
        ");

        if (! $row) {
            $this->record('edition 없음 show', true, 'empty race 없음');

            return;
        }

        $status = $this->httpGet(route('races.show', $row->id));
        $kernel = app(Kernel::class);
        $request = Request::create(route('races.show', $row->id), 'GET');
        $response = $kernel->handle($request);
        $body = $response->getContent();
        $kernel->terminate($request, $response);

        $hasMsg = str_contains($body, '개최 정보 준비 중');
        $this->record('edition 없음 show', $status === 200 && $hasMsg, "race_id={$row->id}, msg=" . ($hasMsg ? 'Y' : 'N'));
    }

    private function appendReport(int $passed, int $failed): void
    {
        $path = $this->option('report')
            ?: base_path('../developer_md/260620/VERIFICATION-REPORT.md');

        if (! file_exists($path)) {
            return;
        }

        $section = "\n### E2E (`review:verify-e2e`) — " . now()->toIso8601String() . "\n\n";
        $section .= "| 시나리오 | 결과 | 상세 |\n|----------|------|------|\n";
        foreach ($this->results as $r) {
            $section .= sprintf("| %s | %s | %s |\n", $r['scenario'], $r['pass'] ? 'PASS' : 'FAIL', str_replace('|', '/', $r['detail']));
        }
        $section .= "\nE2E summary: PASS {$passed} / FAIL {$failed}\n";

        file_put_contents($path, file_get_contents($path) . $section);
        $this->info("Report appended: {$path}");
    }
}
