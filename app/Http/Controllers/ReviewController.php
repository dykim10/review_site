<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Race;
use App\Models\Review;
use App\Services\ReviewService;
use App\Services\SummaryService;
use App\Services\WeatherCaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
        private SummaryService $summaryService,
        private WeatherCaseService $weatherCaseService,
    ) {}

    public function create(Race $race)
    {
        $edition = $race->latestEdition;
        if (! $edition) {
            return redirect()->route('races.show', $race)
                ->with('error', '개최 정보가 없어 후기를 작성할 수 없습니다.');
        }

        if (! $this->reviewService->canCreateReview($edition)) {
            return redirect()->route('races.show', $race)
                ->with('error', '아직 후기 작성 기간이 아닙니다.');
        }

        $existing = $this->reviewService->findUserReview(auth()->user(), $edition);
        if ($existing) {
            return redirect()->route('reviews.edit', $existing);
        }

        $editions = $race->editions()
            ->where('status', 'ended')
            ->where('is_review_open', true)
            ->orderByDesc('year')
            ->get(['id', 'year', 'race_date', 'name']);

        if ($editions->isEmpty()) {
            return redirect()->route('races.show', $race)
                ->with('error', '후기 작성 가능한 개최 회차가 없습니다.');
        }

        return view('reviews.create', compact('race', 'editions', 'edition'));
    }

    public function store(StoreReviewRequest $request, Race $race): RedirectResponse
    {
        $editionId = $request->validated('race_edition_id')
            ?? $race->latestEdition?->id;

        $edition = $race->editions()->findOrFail($editionId);

        if (! $this->reviewService->canCreateReview($edition)) {
            return redirect()->route('races.show', $race)
                ->with('error', '아직 후기 작성 기간이 아닙니다.');
        }

        $existing = $this->reviewService->findUserReview($request->user(), $edition);
        if ($existing) {
            return redirect()->route('reviews.edit', $existing);
        }

        $review = $this->reviewService->create($request->validated(), $request->user(), $edition);
        $this->summaryService->summarize($review, $race);
        $this->summaryService->markRaceSummaryDirty($race);
        $this->weatherCaseService->upsertFromReview($review->fresh(['raceEdition.race', 'raceEdition.weather']));

        return redirect()->route('races.show', $race)
            ->with('success', '리뷰가 등록되었습니다.');
    }

    public function edit(Review $review)
    {
        $this->authorizeReview($review);
        $review->load('raceEdition.race');
        $race     = $review->raceEdition?->race ?? abort(404);
        $editions = $race->editions()->orderByDesc('year')->get(['id', 'year', 'race_date', 'name']);

        return view('reviews.edit', compact('review', 'race', 'editions'));
    }

    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorizeReview($review);

        if (! $review->raceEdition?->canWriteReview()) {
            return redirect()->route('races.show', $review->raceEdition?->race_id ?? abort(404))
                ->with('error', '후기 수정 기간이 아닙니다.');
        }

        $this->reviewService->update($review, $request->validated());
        $review->loadMissing('raceEdition.race');
        if ($review->raceEdition?->race) {
            $this->summaryService->markRaceSummaryDirty($review->raceEdition->race);
        }
        $this->weatherCaseService->upsertFromReview($review->fresh(['raceEdition.race', 'raceEdition.weather']));

        return redirect()->route('races.show', $review->raceEdition?->race_id ?? $review->race_id)
            ->with('success', '리뷰가 수정되었습니다.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorizeReview($review);
        $review->loadMissing('raceEdition.race');
        $raceId = $review->raceEdition?->race_id;
        $race   = $review->raceEdition?->race;
        $this->reviewService->delete($review);

        if ($race) {
            $this->summaryService->markRaceSummaryDirty($race);
        }

        return redirect()->route('races.show', $raceId ?? abort(404))
            ->with('success', '리뷰가 삭제되었습니다.');
    }

    public function parseWatch(Request $request): JsonResponse
    {
        $request->validate([
            'watch_image' => 'required|file|image|max:10240',
        ]);

        try {
            $data = $this->reviewService->parseWatchImage($request->file('watch_image'));

            return response()->json(['ok' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function authorizeReview(Review $review): void
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, '본인의 리뷰만 수정/삭제할 수 있습니다.');
        }
    }
}
