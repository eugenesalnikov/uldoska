<?php

namespace App\Http\Controllers\Listing;

use App\Actions\Listing\StoreListingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Services\ManagedListings;
use App\Services\PhotoOwnerToken;
use Illuminate\Http\RedirectResponse;

class StoreListingController extends Controller
{
    public function __invoke(
        StoreListingRequest $request,
        StoreListingAction  $action,
        PhotoOwnerToken     $ownerToken,
        ManagedListings     $managedListings,
    ): RedirectResponse
    {
        $listing = $action->execute(
            $request->toData($ownerToken->get()),
        );

        $managedListings->allow($listing);

        return redirect()
            ->route('listings.success')
            ->with('success', 'Объявление принято. Осталось подтвердить его в Telegram.');
    }

}
