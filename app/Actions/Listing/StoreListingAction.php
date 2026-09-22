<?php

namespace App\Actions\Listing;

use App\Data\StoreListingData;
use App\Enums\ListingPhotosStatus;
use App\Enums\PendingPhotoStatus;
use App\Events\ListingCreated;
use App\Events\ListingPhotosReady;
use App\Exceptions\DomainException;
use App\Models\Listing;
use App\Models\PendingPhoto;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class StoreListingAction
{
    /**
     * @throws Throwable
     */
    public function execute(
        StoreListingData $data,
    ): Listing
    {
        $listing = DB::transaction(function () use ($data) {
            $listing = Listing::query()->create([
                'district_id'   => $data->districtId,
                'category_id'   => $data->categoryId,
                'title'         => $data->title,
                'body'          => $data->body,
                'price'         => $data->price,
                'photos_status' => $data->photoUuids === []
                    ? ListingPhotosStatus::Ready
                    : ListingPhotosStatus::Pending,
            ]);

            if ($data->photoUuids !== []) {
                $photos = PendingPhoto::query()
                    ->where('owner_token', $data->ownerToken)
                    ->where('status', PendingPhotoStatus::Uploaded)
                    ->whereIn('uuid', $data->photoUuids)
                    ->lockForUpdate()
                    ->get();

                if ($photos->count() !== count($data->photoUuids)) {
                    throw new DomainException('Часть фото недоступна. Загрузите их заново.');
                }

                $photos->each(function (PendingPhoto $photo) use ($listing) {
                    $photo->update([
                        'listing_id' => $listing->id,
                        'status'     => PendingPhotoStatus::Attached,
                    ]);
                });
            }

            return $listing;
        });

        ListingCreated::dispatch($listing->id);

        /**
         * Для объявления без фоток - сразу триггерим тут событие, что фотки "готовы"
         */
        if ($listing->photos_status === ListingPhotosStatus::Ready) {
            ListingPhotosReady::dispatch($listing->id);
        }

        return $listing;
    }

}
