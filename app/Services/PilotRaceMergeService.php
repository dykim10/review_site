<?php

namespace App\Services;

use App\Models\EditionFeedback;
use App\Models\InstagramCache;
use App\Models\Race;
use App\Models\RaceCourse;
use App\Models\RaceEdition;
use App\Models\RaceEntryCategory;
use App\Models\RacePlan;
use App\Models\RaceStats;
use App\Models\RaceWeather;
use App\Models\Review;
use App\Models\YoutubeCache;
use Illuminate\Support\Facades\DB;

/**
 * Pilot orphan races → WA 카탈로그 races 로 edition 이전.
 * edition ID·S3 gpx_url 키(race-courses/{edition_id}/…) 는 유지한다.
 */
class PilotRaceMergeService
{
    public function __construct(private PilotEditionService $pilots) {}

    /**
     * @return list<string>
     */
    public function merge(bool $dryRun = true): array
    {
        $lines = [];

        $run = function () use (&$lines, $dryRun) {
            foreach ($this->pilots->pilotKeys() as $key) {
                $lines = array_merge($lines, $this->mergePilot($key, $dryRun));
            }
        };

        if ($dryRun) {
            $run();
        } else {
            DB::transaction($run);
        }

        return $lines;
    }

    /**
     * @return list<string>
     */
    private function mergePilot(string $key, bool $dryRun): array
    {
        $pilot   = $this->pilots->pilots()[$key];
        $catalog = $this->pilots->catalogRace($key);
        $legacy  = $this->pilots->legacyOrphanRace($key);

        $lines = ["\n[{$key}] {$pilot['name']}"];

        if (! $catalog) {
            $lines[] = '  SKIP — WA 카탈로그 race 없음';

            return $lines;
        }

        if (! $legacy) {
            $lines[] = "  OK — orphan 없음 (카탈로그 races #{$catalog->id} {$catalog->name})";
            if (! $dryRun) {
                $this->pilots->applyPilotRaceDefaults($catalog, $pilot);
            }

            return $lines;
        }

        $lines[] = "  catalog races #{$catalog->id} «{$catalog->name}» (wa={$catalog->wa_label})";
        $lines[] = "  orphan  races #{$legacy->id} «{$legacy->name}»";

        $orphanEditions = RaceEdition::where('race_id', $legacy->id)->orderBy('year')->get();

        if ($orphanEditions->isEmpty()) {
            $lines[] = '  orphan edition 없음 — races 행만 삭제';
            if (! $dryRun) {
                $legacy->delete();
                $this->pilots->applyPilotRaceDefaults($catalog, $pilot);
            }

            return $lines;
        }

        foreach ($orphanEditions as $orphanEdition) {
            $target = RaceEdition::where('race_id', $catalog->id)
                ->where('year', $orphanEdition->year)
                ->first();

            if ($target) {
                $lines[] = "  MERGE edition #{$orphanEdition->id} ({$orphanEdition->year}) → existing #{$target->id}";
                $lines = array_merge(
                    $lines,
                    $this->mergeEditionInto($orphanEdition->id, $target->id, $dryRun)
                );
            } else {
                $lines[] = "  MOVE edition #{$orphanEdition->id} year {$orphanEdition->year} → races #{$catalog->id} (S3 경로 유지: race-courses/{$orphanEdition->id}/…)";
                if (! $dryRun) {
                    $orphanEdition->update([
                        'race_id'     => $catalog->id,
                        'name'        => $catalog->name,
                        'city'        => $pilot['city'],
                        'is_domestic' => true,
                        'country'     => '대한민국',
                    ]);
                }
            }
        }

        $remaining = $dryRun
            ? $orphanEditions->count()
            : RaceEdition::where('race_id', $legacy->id)->count();

        if ($remaining === 0) {
            $lines[] = "  DELETE orphan races #{$legacy->id}";
            if (! $dryRun) {
                $legacy->delete();
            }
        }

        if (! $dryRun) {
            $this->pilots->applyPilotRaceDefaults($catalog->fresh(), $pilot);
        }

        return $lines;
    }

    /**
     * @return list<string>
     */
    private function mergeEditionInto(int $fromEditionId, int $toEditionId, bool $dryRun): array
    {
        $lines = [];

        foreach (RaceCourse::where('race_edition_id', $fromEditionId)->get() as $course) {
            $existing = RaceCourse::where('race_edition_id', $toEditionId)
                ->where('course_type', $course->course_type)
                ->first();

            if (! $existing) {
                $lines[] = "    course {$course->course_type}: #{$course->id} → edition {$toEditionId}";
                if (! $dryRun) {
                    $course->update(['race_edition_id' => $toEditionId]);
                }
            } elseif ($this->courseRicher($course, $existing)) {
                $lines[] = "    course {$course->course_type}: source #{$course->id} replaces target (GPX/좌표 풍부)";
                if (! $dryRun) {
                    $existing->delete();
                    $course->update(['race_edition_id' => $toEditionId]);
                }
            } else {
                $lines[] = "    course {$course->course_type}: drop source #{$course->id} (target 유지, S3 {$existing->gpx_url})";
                if (! $dryRun) {
                    $course->delete();
                }
            }
        }

        foreach (Review::where('race_edition_id', $fromEditionId)->get() as $review) {
            $dup = Review::where('race_edition_id', $toEditionId)
                ->where('user_id', $review->user_id)
                ->exists();
            if ($dup) {
                $lines[] = "    review #{$review->id}: drop (동일 user 리뷰가 target에 있음)";
                if (! $dryRun) {
                    $review->delete();
                }
            } else {
                $lines[] = "    review #{$review->id} → edition {$toEditionId}";
                if (! $dryRun) {
                    $review->update(['race_edition_id' => $toEditionId]);
                }
            }
        }

        $this->reassignRows(RacePlan::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'race_plans');
        $this->reassignRows(EditionFeedback::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'edition_feedback');

        $fromCats = RaceEntryCategory::where('race_edition_id', $fromEditionId)->count();
        if ($fromCats > 0) {
            $lines[] = "    entry_categories: {$fromCats}건 → target (target 기존 삭제 후 이전)";
            if (! $dryRun) {
                RaceEntryCategory::where('race_edition_id', $toEditionId)->delete();
                RaceEntryCategory::where('race_edition_id', $fromEditionId)
                    ->update(['race_edition_id' => $toEditionId]);
            }
        }

        $this->mergeSingleton(RaceWeather::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'race_weather');
        $this->mergeSingleton(RaceStats::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'race_stats');

        $this->reassignRows(YoutubeCache::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'youtube_cache');
        $this->reassignRows(InstagramCache::class, $fromEditionId, $toEditionId, $dryRun, $lines, 'instagram_cache');

        if (! $dryRun) {
            DB::table('review.race_weather_cases')
                ->where('race_edition_id', $fromEditionId)
                ->update(['race_edition_id' => $toEditionId]);
        } else {
            $cnt = DB::table('review.race_weather_cases')->where('race_edition_id', $fromEditionId)->count();
            if ($cnt > 0) {
                $lines[] = "    race_weather_cases: {$cnt}건 → edition {$toEditionId}";
            }
        }

        $lines[] = "    DELETE orphan edition #{$fromEditionId}";
        if (! $dryRun) {
            RaceEdition::where('id', $fromEditionId)->delete();
        }

        return $lines;
    }

    private function courseRicher(RaceCourse $a, RaceCourse $b): bool
    {
        return $this->courseScore($a) > $this->courseScore($b);
    }

    private function courseScore(RaceCourse $c): int
    {
        $score = 0;
        if ($c->gpx_url) {
            $score += 4;
        }
        if (is_array($c->coordinates) && count($c->coordinates) > 0) {
            $score += 3;
        }
        if (is_array($c->elevation_data) && ! empty($c->elevation_data['points'])) {
            $score += 2;
        }

        return $score;
    }

    /**
     * @param  list<string>  $lines
     */
    private function reassignRows(string $model, int $from, int $to, bool $dryRun, array &$lines, string $label): void
    {
        $count = $model::where('race_edition_id', $from)->count();
        if ($count === 0) {
            return;
        }
        $lines[] = "    {$label}: {$count}건 → edition {$to}";
        if (! $dryRun) {
            $model::where('race_edition_id', $from)->update(['race_edition_id' => $to]);
        }
    }

    /**
     * @param  list<string>  $lines
     */
    private function mergeSingleton(string $model, int $from, int $to, bool $dryRun, array &$lines, string $label): void
    {
        $fromRow = $model::where('race_edition_id', $from)->first();
        if (! $fromRow) {
            return;
        }

        $toRow = $model::where('race_edition_id', $to)->first();
        if (! $toRow) {
            $lines[] = "    {$label}: → edition {$to}";
            if (! $dryRun) {
                $fromRow->update(['race_edition_id' => $to]);
            }

            return;
        }

        $lines[] = "    {$label}: drop source (target 유지)";
        if (! $dryRun) {
            $fromRow->delete();
        }
    }
}
