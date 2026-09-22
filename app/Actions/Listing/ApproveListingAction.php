<?php

namespace App\Actions\Listing;

use App\Enums\ListingStatus;
use App\Events\ListingApproved;
use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class ApproveListingAction
{
    /**
     * @throws DomainException
     */
    public function execute(
        Listing $listing,
    ): Listing
    {
        if (!$listing->isReview()) {
            throw new DomainException('Одобрить можно только объявление с модерации.');
        }

        $listing->update([
            'status' => ListingStatus::Approved,
        ]);

        ListingApproved::dispatch($listing->id);

        return $listing;
    }

}
