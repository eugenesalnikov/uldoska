<?php

namespace App\Actions\Listing;

use App\Enums\ListingPublishBlock;
use App\Enums\ListingStatus;
use App\Events\ListingPublishBlockedByLimit;
use App\Events\ListingPublished;
use App\Exceptions\DomainException;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class PublishListingAction
{
    /**
     * @throws DomainException|Throwable
     */
    public function execute(Listing $listing): Listing
    {
        return DB::transaction(function () use ($listing) {
            $listing = Listing::query()
                ->whereKey($listing->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertCanPublish($listing);

            return $this->publish($listing);
        });
    }

    /**
     * @throws Throwable
     */
    public function try(Listing $listing): void
    {
        DB::transaction(function () use ($listing) {
            $listing = Listing::query()
                ->whereKey($listing->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->reasonCannotPublish($listing) !== null) {
                return;
            }

            $this->publish($listing);
        });
    }

    /**
     * @throws DomainException
     */
    private function assertCanPublish(Listing $listing): void
    {
        $reason = $this->reasonCannotPublish($listing);

        if ($reason === null) {
            return;
        }

        if ($reason === ListingPublishBlock::Limit) {
            ListingPublishBlockedByLimit::dispatch($listing);
        }

        throw new DomainException($reason->message());
    }

    private function reasonCannotPublish(Listing $listing): ?ListingPublishBlock
    {
        if ($listing->isPublished()) {
            return ListingPublishBlock::Published;
        }

        if ($listing->isRemoved() || $listing->isRejected()) {
            return ListingPublishBlock::Dead;
        }

        if (!$listing->isApproved() && !$listing->isExpired()) {
            return ListingPublishBlock::Status;
        }

        if (!$listing->hasVerifiedPhone()) {
            return ListingPublishBlock::Phone;
        }

        if (!$listing->arePhotosReady()) {
            return ListingPublishBlock::Photos;
        }

        if ($listing->hasReachedPublishedLimit()) {
            return ListingPublishBlock::Limit;
        }

        return null;
    }

    private function publish(Listing $listing): Listing
    {
        $listing->update([
            'status'       => ListingStatus::Published,
            'published_at' => $listing->published_at ?? now(),
            'expires_at'   => now()->addDays(config('uldoska.listing_ttl_days', 14)),
        ]);

        ListingPublished::dispatch($listing);

        return $listing;
    }

}
