<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Models\Listing;
use RuntimeException;

class ManageListingAction
{
    public function extend(Listing $listing): Listing
    {
        if (!$listing->canExtend()) {
            throw new RuntimeException('Продлить можно за 3 дня до окончания или после истечения срока.');
        }

        $addDays = config('uldoska.listing_ttl_days', 14);

        $from = $listing->expires_at?->isFuture()
            ? $listing->expires_at
            : now();

        $listing->update([
            'expires_at' => $from->copy()->addDays($addDays),
        ]);

        return $listing;
    }

    public function remove(Listing $listing): Listing
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
