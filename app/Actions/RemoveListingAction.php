<?php

namespace App\Actions;

use App\Enums\ListingInterestStatus;
use App\Enums\ListingStatus;
use App\Events\ListingRemoved;
use App\Models\Listing;
use App\Models\ListingInterest;
use Illuminate\Support\Facades\DB;

final readonly class RemoveListingAction
{
    public function execute(Listing $listing): Listing
    {
        if ($listing->isRemoved()) {
            return $listing;
        }

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

        ListingRemoved::dispatch($listing, $chatIds);

        return $listing;
    }

}
