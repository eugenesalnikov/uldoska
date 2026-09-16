<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Events\ListingPublishBlockedByLimit;
use App\Events\ListingPublished;
use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class PublishListingAction
{
    /**
     * @throws DomainException
     */
    public function execute(Listing $listing): Listing
    {
        if ($listing->isPublished()) {
            throw new DomainException('Объявление уже в ленте.');
        }

        if ($listing->isRemoved() || $listing->isRejected()) {
            throw new DomainException('Это объявление нельзя вернуть в ленту.');
        }

        if (!$listing->isReview() && !$listing->isExpired()) {
            throw new DomainException('В ленту можно пустить только объявление с модерации или истекшее.');
        }

        if (!$listing->isBoundToTelegram()) {
            throw new DomainException('Сначала объявление должно быть подтверждено в Telegram.');
        }

        if ($listing->hasReachedPublishedLimit()) {
            ListingPublishBlockedByLimit::dispatch($listing);

            throw new DomainException('Можно держать не больше 5 объявлений на доске одновременно.');
        }

        $listing->update([
            'status'       => ListingStatus::Published,
            'published_at' => $listing->published_at ?? now(),
            'expires_at'   => now()->addDays(config('uldoska.listing_ttl_days', 14)),
        ]);

        ListingPublished::dispatch($listing);

        return $listing;
    }

}
