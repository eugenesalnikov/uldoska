<?php

namespace App\Actions;

use App\Enums\ListingRejectionReason;
use App\Enums\ListingStatus;
use App\Events\ListingRejected;
use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class RejectListingAction
{
    /**
     * @throws DomainException
     */
    public function execute(
        Listing                $listing,
        ListingRejectionReason $reason,
        ?string                $comment = null,
    ): Listing
    {
        if (!$listing->isReview()) {
            throw new DomainException('Отклонить можно только объявление на модерации.');
        }

        $listing->update([
            'status'            => ListingStatus::Rejected,
            'rejection_reason'  => $reason,
            'rejection_comment' => $comment,
        ]);

        ListingRejected::dispatch($listing);

        return $listing;
    }

}
