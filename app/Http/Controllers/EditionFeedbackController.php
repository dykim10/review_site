<?php

namespace App\Http\Controllers;

use App\Models\EditionFeedback;
use App\Models\RaceEdition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditionFeedbackController extends Controller
{
    public function store(Request $request, RaceEdition $edition): RedirectResponse
    {
        abort_unless($edition->isUpcoming(), 403, '대회 종료 후에는 후기 게시판을 이용해주세요.');

        $validated = $request->validate([
            'content'  => 'required|string|max:5000',
            'category' => 'nullable|in:course,ops,registration,other',
        ]);

        EditionFeedback::create([
            ...$validated,
            'user_id'         => auth()->id(),
            'race_edition_id' => $edition->id,
        ]);

        return back()->with('success', '의견이 등록되었습니다.');
    }
}
