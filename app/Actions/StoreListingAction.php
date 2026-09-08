<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;

class StoreListingAction
{
    public function execute(StoreListingRequest $request): Listing
    {
        $listing = Listing::query()->create([
            'district_id' => $request->integer('district_id'),
            'category_id' => $request->integer('category_id'),
            'title'       => $request->string('title')->toString(),
            'body'        => $request->string('body')->toString(),
            'price'       => $request->input('price'),
            'phone'       => $request->string('phone')->toString(),
        ]);

        foreach ($request->file('photos', []) as $photo) {
            $listing->addMedia($photo)->toMediaCollection('photos');
        }

        return $listing;
    }

}
