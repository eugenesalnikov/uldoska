<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Models\Listing;
use RuntimeException;

class PublishListingAction
{
    public function execute(Listing $listing): Listing
    {
        if (!$listing->isReview()) {
            throw new RuntimeException('В ленту можно пустить только объявление с модерации.');
        }

        $listing->update([
            'status' => ListingStatus::Published,
            'published_at' => now(),
            'expires_at' => now()->addDays(config('uldoska.listing_ttl_days', 14)),
        ]);

        return $listing;
    }

}
