<?php

namespace App\Actions\Listing;

use App\Enums\ListingInterestStatus;
use App\Enums\ListingStatus;
use App\Events\ListingPublishSlotFreed;
use App\Events\ListingRemoved;
use App\Models\Listing;
use App\Models\ListingInterest;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RemoveListingAction
{
    /**
     * @throws Throwable
     */
    public function execute(Listing $listing): Listing
    {
        if ($listing->isRemoved()) {
            return $listing;
        }

        $wasPublished = $listing->isPublished();
        $chatId = $listing->telegram_chat_id;

        $chatIds = DB::transaction(function () use ($listing) {
            $chatIds = ListingInterest::query()
                ->where('listing_id', $listing->id)
                ->where('status', ListingInterestStatus::Pending)
                ->pluck('interested_chat_id', 'id');

            $listing->update([
                'status' => ListingStatus::Removed,
                'phone'  => null,
            ]);

            if ($chatIds->isNotEmpty()) {
                ListingInterest::query()
                    ->whereIn('id', $chatIds->keys())
                    ->update(['status' => ListingInterestStatus::Cancelled]);
            }

            return $chatIds->values();
        });

        if ($wasPublished && filled($chatId)) {
            ListingPublishSlotFreed::dispatch($chatId);
        }

        ListingRemoved::dispatch($listing, $chatIds);

        return $listing;
    }

}
