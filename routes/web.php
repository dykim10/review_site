<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CompletionRecordController;
use App\Http\Controllers\Admin\RaceController as AdminRaceController;
use App\Http\Controllers\Admin\RaceEditionController as AdminRaceEditionController;
use App\Http\Controllers\Admin\RaceCourseController as AdminRaceCourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RaceController::class, 'index'])->name('home');

// 이메일 인증은 회원가입 시에만 필요 — 로그인 후 재접속은 인증 불필요
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 공개 - 대회 목록/상세
Route::get('/races', [RaceController::class, 'index'])->name('races.index');
Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');

// 완주 기록 - 인증 필요
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/races/{race}/completions', [CompletionRecordController::class, 'store'])->name('completions.store');
    Route::delete('/completions/{record}', [CompletionRecordController::class, 'destroy'])->name('completions.destroy');
});

// 리뷰 - 인증 필요
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/races/{race}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/races/{race}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// 관리자 - 대회 CRUD
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('races', AdminRaceController::class);
    Route::resource('race-editions', AdminRaceEditionController::class);
    Route::resource('race-courses', AdminRaceCourseController::class)->only(['index', 'create', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
