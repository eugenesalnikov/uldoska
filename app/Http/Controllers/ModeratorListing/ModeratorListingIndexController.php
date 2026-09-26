<?php

namespace App\Http\Controllers\ModeratorListing;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Contracts\View\View;

class ModeratorListingIndexController extends Controller
{
    public function __invoke(): View
    {
        return view('moderator.index', [
            'listings' => Listing::query()
                ->where('status', ListingStatus::Review)
                ->with(['district', 'category'])
                ->withCount([
                    'telegramListings as published_on_telegram_count' => function ($query) {
                        $query->where('status', ListingStatus::Published);
                    },
                ])
                ->latest()
                ->get(),
        ]);
    }

}
