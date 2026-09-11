<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Models\Listing;

readonly class RemoveListingAction
{
    public function execute(Listing $listing): Listing
    {
        if ($listing->isRemoved()) {
            return $listing;
        }

        $listing->update([
            'status' => ListingStatus::Removed,
        ]);

        return $listing;
    }

}
