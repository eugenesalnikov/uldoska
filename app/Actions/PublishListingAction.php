<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Events\ListingPublishBlockedByLimit;
use App\Exceptions\DomainException;
use App\Exceptions\ListingActiveLimitReachedException;
use App\Models\Listing;
use RuntimeException;

class PublishListingAction
{
    /**
     * @throws DomainException
     */
    public function execute(Listing $listing): Listing
    {
        if ($listing->isRemoved() || $listing->isRejected()) {
            throw new DomainException('Это объявление нельзя вернуть в ленту.');
        }

        if (!$listing->isReview() && !$listing->isExpired()) {
            throw new RuntimeException('В ленту можно пустить только объявление с модерации или истекшее.');
        }

        if (blank($listing->telegram_chat_id)) {
            throw new RuntimeException('Сначала объявление должно быть подтверждено в Telegram.');
        }

        if ($listing->hasReachedPublishedLimit()) {
            ListingPublishBlockedByLimit::dispatch($listing);

            throw new ListingActiveLimitReachedException($listing);
        }

        $listing->update([
            'status'       => ListingStatus::Published,
            'published_at' => $listing->published_at ?? now(),
            'expires_at'   => now()->addDays(config('uldoska.listing_ttl_days', 14)),
        ]);

        return $listing;
    }

}
