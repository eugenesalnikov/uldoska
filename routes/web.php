<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Listing\CreateListingController;
use App\Http\Controllers\Listing\ExtendListingController;
use App\Http\Controllers\Listing\GoController;
use App\Http\Controllers\Listing\IndexListingController;
use App\Http\Controllers\Listing\ManageListingController;
use App\Http\Controllers\Listing\RemoveListingController;
use App\Http\Controllers\Listing\ShowListingController;
use App\Http\Controllers\Listing\StoreListingController;
use App\Http\Controllers\Listing\SuccessListingController;
use App\Http\Controllers\ModeratorListing\ModeratorListingApproveController;
use App\Http\Controllers\ModeratorListing\ModeratorListingIndexController;
use App\Http\Controllers\ModeratorListing\ModeratorListingRejectController;
use App\Http\Controllers\ModeratorListing\ModeratorListingShowController;
use App\Http\Controllers\PendingPhotoController;
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

Route::get('/l', IndexListingController::class)->name('listings.index');
Route::get('/l/{listing}', ShowListingController::class)->name('listings.show');
Route::get('/r/{district:slug}/l', IndexListingController::class)->name('listings.district');
Route::get('/c/{category:slug}', IndexListingController::class)->name('listings.category');
Route::get('/r/{district:slug}/c/{category:slug}', IndexListingController::class)
    ->withoutScopedBindings()
    ->name('listings.district.category');
Route::get('/go', GoController::class)->name('listings.go');

/**
 * Listings submit routes
 */

Route::get('/submit', CreateListingController::class)
    ->middleware('noindex')
    ->name('listings.create');
Route::post('/submit', StoreListingController::class)
    ->middleware(['throttle:5,1', 'noindex'])
    ->name('listings.store');
Route::get('/submit/done', SuccessListingController::class)
    ->middleware('noindex')
    ->name('listings.success');

/**
 * Listing management routes
 */

Route::middleware(['manage', 'noindex', 'throttle:20,1'])->prefix('m')->group(function () {
    Route::get('/{listing:public_code}', ManageListingController::class)->name('listings.manage');
    Route::post('/{listing:public_code}/extend', ExtendListingController::class)->name('listings.extend');
    Route::post('/{listing:public_code}/remove', RemoveListingController::class)->name('listings.remove');
});

/**
 * Listing moderation routes
 */

Route::middleware(['moderator', 'noindex', 'throttle:30,1'])->prefix('mod')->group(function () {
    Route::get('/', ModeratorListingIndexController::class)->name('moderator.index');
    Route::get('/{listing}', ModeratorListingShowController::class)->name('moderator.show');
    Route::post('/{listing}/approve', ModeratorListingApproveController::class)->name('moderator.approve');
    Route::post('/{listing}/reject', ModeratorListingRejectController::class)->name('moderator.reject');
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

/**
 * Pending photo upload routes
 */

Route::post('/photos', [PendingPhotoController::class, 'store'])
    ->name('photos.store');
Route::delete('/photos/{pendingPhoto}', [PendingPhotoController::class, 'destroy'])
    ->name('photos.destroy');
Route::get('/photos/{pendingPhoto}/file', [PendingPhotoController::class, 'show'])
    ->name('photos.show');
