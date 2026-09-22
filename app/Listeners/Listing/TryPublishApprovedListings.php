<?php

namespace App\Listeners\Listing;

use App\Actions\Listing\PublishListingAction;
use App\Enums\ListingStatus;
use App\Events\ListingPublishSlotFreed;
use App\Models\Listing;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class TryPublishApprovedListings implements ShouldQueue
{
    public function __construct(
        private PublishListingAction $publishListing,
    )
    {
    }

    public function handle(
        ListingPublishSlotFreed $event,
    ): void
    {
        Listing::query()
            ->where('telegram_chat_id', $event->telegramChatId)
            ->where('status', ListingStatus::Approved)
            ->orderBy('id')
            ->each(fn(Listing $listing) => $this->publishListing->try($listing));
    }

}
