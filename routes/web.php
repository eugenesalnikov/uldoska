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
Route::get('/c/{category:slug}', [ListingController::class, 'index'])->name('listings.category');
Route::get('/r/{district:slug}/c/{category:slug}', [ListingController::class, 'index'])
    ->withoutScopedBindings()
    ->name('listings.district.category');
Route::get('/go', [ListingController::class, 'go'])->name('listings.go');

/**
 * Listings submit routes
 */

Route::get('/submit', [ListingController::class, 'create'])
    ->middleware('noindex')
    ->name('listings.create');
Route::post('/submit', [ListingController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('listings.store');
Route::get('/submit/{listing}/done', [ListingController::class, 'success'])->name('listings.success');

/**
 * Listing management routes
 */

Route::middleware(['manage', 'noindex', 'throttle:20,1'])->prefix('m')->group(function () {
    Route::get('/{listing:public_code}', [ListingController::class, 'manage'])->name('listings.manage');
    Route::post('/{listing:public_code}/extend', [ListingController::class, 'extend'])->name('listings.extend');
    Route::post('/{listing:public_code}/remove', [ListingController::class, 'remove'])->name('listings.remove');
});

/**
 * Listing moderation routes
 */

Route::middleware(['moderator', 'throttle:30,1'])->prefix('mod')->group(function () {
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
Route::view('/privacy', 'pages.privacy')->name('pages.privacy');

/**
 * Sitemap route
 */

Route::get('/sitemap.xml', function () {
    abort_unless(file_exists(storage_path('app/sitemap.xml')), 404);

    return response()->file(storage_path('app/sitemap.xml'), [
        'Content-Type' => 'application/xml',
    ]);
});
