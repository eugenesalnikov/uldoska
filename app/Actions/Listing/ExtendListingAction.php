<?php

namespace App\Actions\Listing;

use App\Exceptions\DomainException;
use App\Models\Listing;
use Throwable;

final readonly class ExtendListingAction
{
    public function __construct(
        private PublishListingAction $publish,
    )
    {
    }

    /**
     * @throws DomainException|Throwable
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
