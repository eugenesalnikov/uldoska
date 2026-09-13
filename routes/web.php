<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ModeratorController;
use App\Services\CurrentDistrict;
use Illuminate\Support\Facades\Route;

/**
 * Home routes
 */
Route::get('/', HomeController::class)->name('home');
Route::get('/r/all', function (CurrentDistrict $currentDistrict) {
    $currentDistrict->forget();
    return redirect()->route('home');
})->name('district.all');
Route::get('/r/{district:slug}', HomeController::class)->name('home.district');


// 'slug' => ['required', 'unique:districts,slug', Rule::notIn(['all', 'r', 'c', 'listings', 'admin'])]

/**
 * Listings view routes
 */

Route::get('/l', [ListingController::class, 'index'])->name('listings.index');
Route::get('/l/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/r/{district:slug}/l', [ListingController::class, 'index'])->name('listings.district');
Route::get('/c/{category:slug}/l', [ListingController::class, 'index'])->name('listings.category');
Route::get('/r/{district:slug}/c/{category:slug}/l', [ListingController::class, 'index'])
    ->withoutScopedBindings()
    ->name('listings.district.category');
Route::get('/go', [ListingController::class, 'go'])->name('listings.go');

/**
 * Listings submit routes
 */

Route::get('/submit', [ListingController::class, 'create'])->name('listings.create');
Route::post('/submit', [ListingController::class, 'store'])->name('listings.store');
Route::get('/submit/{listing}/done', [ListingController::class, 'success'])->name('listings.success');

/**
 * Listing management routes
 */

Route::get('/m/{listing:manage_token}', [ListingController::class, 'manage'])->name('listings.manage');
Route::post('/m/{listing:manage_token}/extend', [ListingController::class, 'extend'])->name('listings.extend');
Route::post('/m/{listing:manage_token}/remove', [ListingController::class, 'remove'])->name('listings.remove');

/**
 * Listing moderation routes
 */

Route::middleware('moderator')->prefix('mod')->group(function () {
    Route::get('/', [ModeratorController::class, 'index'])->name('moderator.index');
    Route::get('/{listing}', [ModeratorController::class, 'show'])->name('moderator.show');
    Route::post('/{listing}/publish', [ModeratorController::class, 'publish'])->name('moderator.publish');
    Route::post('/{listing}/reject', [ModeratorController::class, 'reject'])->name('moderator.reject');
});

/**
 * Static pages routes
 */

Route::view('/rules', 'pages.rules')->name('pages.rules');
Route::view('/about', 'pages.about')->name('pages.about');
