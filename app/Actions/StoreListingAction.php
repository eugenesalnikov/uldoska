<?php

namespace App\Actions;

use App\Data\StoreListingData;
use App\Models\Listing;

final readonly class StoreListingAction
{
    public function execute(StoreListingData $data): Listing
    {
        $listing = Listing::query()
            ->create([
                'district_id' => $data->districtId,
                'category_id' => $data->categoryId,
                'title'       => $data->title,
                'body'        => $data->body,
                'price'       => $data->price,
            ]);

        foreach ($data->photoPaths as $path) {
            $listing->addMedia($path)->toMediaCollection('photos');
        }

        return $listing;
    }

}
