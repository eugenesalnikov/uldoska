<?php

namespace App\Http\Controllers\Listing;

use App\Actions\Listing\ExtendListingAction;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Throwable;

class ExtendListingController extends Controller
{
    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function __invoke(
        Listing             $listing,
        ExtendListingAction $action
    ): RedirectResponse
    {
        $action->execute($listing);

        return back()->with('success', 'Объявление продлено.');
    }

}
