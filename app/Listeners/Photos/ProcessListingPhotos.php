<?php

namespace App\Listeners\Photos;

use App\Enums\PendingPhotoStatus;
use App\Events\ListingCreated;
use App\Jobs\EnsureListingPhotosReadyJob;
use App\Models\Listing;
use App\Models\PendingPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Imagick;
use ImagickException;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

final readonly class ProcessListingPhotos implements ShouldQueue
{
    public function handle(
        ListingCreated $event,
    ): void
    {
        $listing = Listing::query()->find($event->listingId);

        if ($listing === null) {
            return;
        }

        $photos = PendingPhoto::query()
            ->where('listing_id', $listing->id)
            ->where('status', PendingPhotoStatus::Attached)
            ->get();

        $photos->each(fn(PendingPhoto $photo) => $this->process($listing, $photo));

        EnsureListingPhotosReadyJob::dispatch($listing->id);
    }

    private function process(Listing $listing, PendingPhoto $photo): void
    {
        $path = $photo->absolutePath();

        try {
            $image = new Imagick($path);
            $image->stripImage();
            $image->autoOrient();

            $format = strtolower($image->getImageFormat());
            $extension = match ($format) {
                'jpeg', 'jpg' => 'jpg',
                'png'         => 'png',
                'webp'        => 'webp',
                'gif'         => 'gif',
                default       => throw new ImagickException('Unsupported image format'),
            };

            $image->setImageFormat($extension);

            $cleanedPath = $path . '.' . $extension;
            $image->writeImage($cleanedPath);
            $image->clear();
            unset($image);

            $listing
                ->addMedia($cleanedPath)
                ->usingFileName($photo->uuid . '.' . $extension)
                ->toMediaCollection('photos');

            $photo->delete();

            if (file_exists($cleanedPath)) {
                unlink($cleanedPath);
            }
        } catch (FileDoesNotExist|FileIsTooBig|ImagickException|Throwable) {
            $photo->update(['status' => PendingPhotoStatus::Failed]);

            throw new RuntimeException(
                "Не удалось обработать фото $photo->uuid объявления $listing->id"
            );
        }
    }

}
