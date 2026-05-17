<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\RaceController as AdminRaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 공개 - 대회 목록/상세
Route::get('/races', [RaceController::class, 'index'])->name('races.index');
Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');

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
});

require __DIR__.'/auth.php';
