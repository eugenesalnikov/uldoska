<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Events\ListingExpired;
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

        return $listings;
    }

}
