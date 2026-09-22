<?php

namespace App\Jobs;

use App\Enums\ListingPhotosStatus;
use App\Events\ListingPhotosReady;
use App\Models\Listing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class EnsureListingPhotosReadyJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 30;

    public function __construct(
        public int $listingId,
    )
    {
    }

    public function handle(): void
    {
        $listing = Listing::query()->find($this->listingId);

        if ($listing === null || $listing->photos_status === ListingPhotosStatus::Ready) {
            return;
        }

        $photos = $listing->getMedia('photos');

        $allReady = $photos->isNotEmpty()
            && $photos->every(
                fn($media) => $media->hasGeneratedConversion('thumb')
                    && $media->hasGeneratedConversion('show')
            );

        if (!$allReady) {
            $this->release(2);
            return;
        }

        $updated = Listing::query()
            ->whereKey($listing->id)
            ->where('photos_status', '!=', ListingPhotosStatus::Ready)
            ->update(['photos_status' => ListingPhotosStatus::Ready]);

        if ($updated === 0) {
            return;
        }

        ListingPhotosReady::dispatch($listing->id);
    }

}
