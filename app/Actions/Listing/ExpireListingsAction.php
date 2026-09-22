<?php

namespace App\Actions\Listing;

use App\Enums\ListingStatus;
use App\Events\ListingExpired;
use App\Events\ListingPublishSlotFreed;
use App\Models\Listing;
use Illuminate\Support\Collection;

final readonly class ExpireListingsAction
{
    public function execute(): Collection
    {
        $listings = Listing::query()
            ->dueToExpire()
            ->get();

        if ($listings->isEmpty()) {
            return $listings;
        }

        Listing::query()
            ->whereIn('id', $listings->modelKeys())
            ->update(['status' => ListingStatus::Expired]);

        $listings->each(function (Listing $listing) {
            $listing->status = ListingStatus::Expired;

            ListingExpired::dispatch($listing);
        });

        $listings
            ->pluck('telegram_chat_id')
            ->filter()
            ->unique()
            ->each(fn(string $chatId) => ListingPublishSlotFreed::dispatch($chatId));

        return $listings;
    }

}
