<?php

namespace App\Actions;

use App\Exceptions\DomainException;
use App\Models\Listing;

readonly class ExtendListingAction
{
    public function __construct(
        private PublishListingAction $publish,
    )
    {
    }

    /**
     * @throws DomainException
     */
    public function execute(Listing $listing): Listing
    {
        if (!$listing->canExtend()) {
            throw new DomainException('Продлить можно за 3 дня до окончания или после истечения срока.');
        }

        if ($listing->isPublished()) {
            $addDays = config('uldoska.listing_ttl_days', 14);
            $from = $listing->expires_at?->isFuture()
                ? $listing->expires_at
                : now();

            $listing->update([
                'expires_at' => $from->copy()->addDays($addDays),
            ]);

            return $listing;
        }

        if ($listing->isExpired()) {
            return $this->publish->execute($listing);
        }

        throw new DomainException('Это объявление нельзя продлить.');
    }

}
