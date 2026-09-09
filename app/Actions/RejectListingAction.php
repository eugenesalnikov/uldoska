<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Models\Listing;
use RuntimeException;

class RejectListingAction
{
    public function execute(Listing $listing): Listing
    {
        if (!$listing->isReview()) {
            throw new RuntimeException('Отклонить можно только объявление на модерации.');
        }

        $listing->update([
            'status' => ListingStatus::Rejected,
        ]);

        return $listing;
    }

}
