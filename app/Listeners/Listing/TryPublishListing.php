<?php

namespace App\Listeners\Listing;

use App\Actions\Listing\PublishListingAction;
use App\Events\ListingApproved;
use App\Events\ListingPhoneVerified;
use App\Events\ListingPhotosReady;
use App\Models\Listing;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class TryPublishListing implements ShouldQueue
{
    public function __construct(
        private PublishListingAction $publishListing,
    )
    {
    }

    public function handle(
        ListingApproved|ListingPhoneVerified|ListingPhotosReady $event,
    ): void
    {
        $listing = Listing::query()->find($event->listingId);

        if ($listing === null) {
            return;
        }

        $this->publishListing->try($listing);
    }

}
