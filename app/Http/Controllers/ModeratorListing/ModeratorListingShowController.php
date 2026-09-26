<?php

namespace App\Http\Controllers\ModeratorListing;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Contracts\View\View;

class ModeratorListingShowController extends Controller
{
    public function __invoke(Listing $listing): View
    {
        abort_unless($listing->isReview(), 404);
        $listing->load(['district', 'category', 'media']);

        return view('moderator.show', compact('listing'));
    }

}
