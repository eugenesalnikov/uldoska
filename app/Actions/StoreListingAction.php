<?php

namespace App\Actions;

use App\Data\StoreListingData;
use App\Exceptions\DomainException;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;
use Imagick;
use ImagickException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

final readonly class StoreListingAction
{
    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws ImagickException
     * @throws DomainException
     */
    public function execute(StoreListingData $data): Listing
    {
        $listing = DB::transaction(function () use ($data) {
            $listing = Listing::query()
                ->create([
                    'district_id' => $data->districtId,
                    'category_id' => $data->categoryId,
                    'title'       => $data->title,
                    'body'        => $data->body,
                    'price'       => $data->price,
                ]);

            foreach ($data->photoPaths as $path) {
                try {
                    $media = $listing->addMedia($path)->toMediaCollection('photos');

                    $image = new Imagick($media->getPath());
                    $image->stripImage();
                    $image->writeImage($media->getPath());
                    $image->clear();
                } catch (
                FileDoesNotExist|
                FileIsTooBig|
                ImagickException
                ) {
                    throw new DomainException('Не удалось обработать одно из фото. Загрузите другой файл.');
                }
            }

            return $listing;
        });

        return $listing;
    }

}
