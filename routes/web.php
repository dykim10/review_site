<?php

use App\Http\Controllers\AdminPasswordConfirmController;
use App\Http\Controllers\EditionFeedbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\RaceController as AdminRaceController;
use App\Http\Controllers\Admin\RaceEditionController as AdminRaceEditionController;
use App\Http\Controllers\Admin\RaceCourseController as AdminRaceCourseController;
use App\Http\Controllers\Admin\PilotEditionController;
use App\Http\Controllers\Admin\WaLabelSyncController;
use App\Http\Controllers\RacePlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RaceController::class, 'index'])->name('home');

// 개인정보처리방침 (공개)
Route::view('/privacy', 'privacy.index')->name('privacy');

// 이메일 인증은 회원가입 시에만 필요 — 로그인 후 재접속은 인증 불필요
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password-reset', [ProfileController::class, 'sendPasswordResetLink'])
        ->middleware('throttle:6,1')
        ->name('profile.password-reset');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 공개 - 대회 목록/상세
Route::get('/races', [RaceController::class, 'index'])->name('races.index');
Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');
Route::get('/races/{race}/editions/{edition}', [RaceController::class, 'showEdition'])->name('races.show-edition');

// 리뷰 - 인증 필요
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/editions/{edition}/feedback', [EditionFeedbackController::class, 'store'])
        ->name('editions.feedback.store');

    Route::get('/editions/{edition}/plans', [RacePlanController::class, 'index'])->name('race-plan.index');
    Route::get('/plans/{plan}', [RacePlanController::class, 'show'])->name('race-plan.show');

    Route::get('/races/{race}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/races/{race}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/parse-watch', [ReviewController::class, 'parseWatch'])->name('reviews.parse-watch');

    // 레이스 플랜
    Route::get('/races/{race}/race-plan', [RacePlanController::class, 'create'])->name('race-plan.create');
    Route::post('/races/{race}/race-plan/generate', [RacePlanController::class, 'generate'])->name('race-plan.generate');
});

// 관리자 비밀번호 재확인 + Filament 접근 거부 안내 (Filament /admin 밖)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin-forbidden', fn () => view('admin.forbidden'))->name('admin.forbidden');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin-confirm', [AdminPasswordConfirmController::class, 'show'])->name('admin.password.confirm');
    Route::post('/admin-confirm', [AdminPasswordConfirmController::class, 'store'])->name('admin.password.confirm.store');
});

// 손제작 대회관리 — Filament /admin 과 경로 분리 (병행 이관)
Route::prefix('races-admin')->name('races-admin.')->middleware(['auth', 'verified', 'admin', 'admin.password'])->group(function () {
    Route::get('/', fn () => redirect()->route('races-admin.races.index'))->name('home');
    Route::resource('races', AdminRaceController::class);
    Route::get('race-editions/{race_edition}/clone-next', [AdminRaceEditionController::class, 'cloneNext'])
        ->name('race-editions.clone-next');
    Route::get('race-editions/{race_edition}/clone-previous', [AdminRaceEditionController::class, 'clonePrevious'])
        ->name('race-editions.clone-previous');
    Route::get('race-editions/{race_edition}/entry-categories', [AdminRaceEditionController::class, 'entryCategoriesJson'])
        ->name('race-editions.entry-categories');
    Route::resource('race-editions', AdminRaceEditionController::class);
    Route::resource('race-courses', AdminRaceCourseController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::post('wa-label/preview', [WaLabelSyncController::class, 'preview'])->name('wa-label.preview');
    Route::post('wa-label/sync', [WaLabelSyncController::class, 'sync'])->name('wa-label.sync');
    Route::post('wa-label/cancel', [WaLabelSyncController::class, 'cancel'])->name('wa-label.cancel');

    Route::post('pilot-editions/preview', [PilotEditionController::class, 'preview'])->name('pilot-editions.preview');
    Route::post('pilot-editions/provision', [PilotEditionController::class, 'provision'])->name('pilot-editions.provision');
    Route::post('pilot-editions/attach-gpx', [PilotEditionController::class, 'attachGpx'])->name('pilot-editions.attach-gpx');
});

require __DIR__.'/auth.php';
