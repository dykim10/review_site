<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Race;
use App\Models\Review;
use App\Services\ReviewService;
use App\Services\SummaryService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
        private SummaryService $summaryService,
    ) {}

    public function create(Race $race)
    {
        if ($this->reviewService->alreadyReviewed(auth()->user(), $race)) {
            return redirect()->route('races.show', $race)
                ->with('error', '이미 이 대회에 리뷰를 작성하셨습니다.');
        }

        return view('reviews.create', compact('race'));
    }

    public function store(StoreReviewRequest $request, Race $race): RedirectResponse
    {
        if ($this->reviewService->alreadyReviewed($request->user(), $race)) {
            return redirect()->route('races.show', $race)
                ->with('error', '이미 이 대회에 리뷰를 작성하셨습니다.');
        }

        $review = $this->reviewService->create($request->validated(), $request->user(), $race);
        $this->summaryService->summarize($review, $race);
        $this->summaryService->summarizeRace($race);

        return redirect()->route('races.show', $race)
            ->with('success', '리뷰가 등록되었습니다.');
    }

    public function edit(Review $review)
    {
        $this->authorizeReview($review);
        return view('reviews.edit', compact('review'));
    }

    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorizeReview($review);
        $this->reviewService->update($review, $request->validated());
        $race = Race::find($review->race_id);
        if ($race) {
            $this->summaryService->summarizeRace($race);
        }

        return redirect()->route('races.show', $review->race_id)
            ->with('success', '리뷰가 수정되었습니다.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorizeReview($review);
        $race = Race::find($review->race_id);
        $this->reviewService->delete($review);
        if ($race) {
            $this->summaryService->summarizeRace($race);
        }

        return redirect()->route('races.show', $review->race_id)
            ->with('success', '리뷰가 삭제되었습니다.');
    }

    private function authorizeReview(Review $review): void
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, '본인의 리뷰만 수정/삭제할 수 있습니다.');
        }
    }
}
