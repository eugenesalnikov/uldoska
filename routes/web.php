<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
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
 * Listings routes
 */

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/r/{district:slug}/listings', [ListingController::class, 'index'])->name('listings.district');
Route::get('/c/{category:slug}/listings', [ListingController::class, 'index'])->name('listings.category');
Route::get('/r/{district:slug}/c/{category:slug}/listings', [ListingController::class, 'index'])
    ->withoutScopedBindings()
    ->name('listings.district.category');
Route::get('/go', [ListingController::class, 'go'])->name('listings.go');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/submit', [ListingController::class, 'create'])->name('listings.create');
Route::post('/submit', [ListingController::class, 'store'])->name('listings.store');
Route::get('/submit/{listing}/done', [ListingController::class, 'success'])->name('listings.success');



Route::view('/rules', 'pages.rules')->name('pages.rules');
Route::view('/about', 'pages.about')->name('pages.about');
