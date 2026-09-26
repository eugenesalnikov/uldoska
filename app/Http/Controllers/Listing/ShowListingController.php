<?php

namespace App\Http\Controllers\Listing;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShowListingController extends Controller
{
    public function __invoke(Listing $listing): View|RedirectResponse
    {
        if ($listing->isPublished()) {
            $listing->load(['district', 'category', 'media']);

            $districtName = $listing->district
                ? $listing->district->name . ', Ульяновск'
                : 'Ульяновск';

            $heading = $listing->title;
            $title = "$listing->title – {$listing->category->name}, $districtName | Uldoska";
            $description = $listing->seoDescription();

            return view('listings.show', [
                'listing'     => $listing,
                'heading'     => $heading,
                'title'       => $title,
                'description' => $description,
            ]);
        }

        if ($listing->statusIn([ListingStatus::Expired, ListingStatus::Removed]) && $listing->category) {
            return redirect()->route('listings.district.category', [
                'category' => $listing->category,
                'district' => $listing->district,
            ], 301);
        }

        abort(404);
    }

}
