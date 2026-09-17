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
                    $image = new Imagick($path);
                    $image->stripImage();

                    $format = strtolower($image->getImageFormat());
                    $extension = match ($format) {
                        'jpeg', 'jpg' => 'jpg',
                        'png' => 'png',
                        'webp' => 'webp',
                        'gif' => 'gif',
                        default => throw new DomainException('Не удалось обработать одно из фото. Загрузите другой файл.'),
                    };

                    $image->setImageFormat($extension);
                    $cleanedPath = $path . '.' . $extension;
                    $image->writeImage($cleanedPath);
                    $image->clear();
                    $image->destroy();

                    $listing
                        ->addMedia($cleanedPath)
                        ->usingFileName(basename($cleanedPath))
                        ->toMediaCollection('photos');
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
