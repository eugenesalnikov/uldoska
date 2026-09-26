<?php

namespace App\Http\Controllers\Listing;

use App\Actions\Listing\RemoveListingAction;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Throwable;

class RemoveListingController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        Listing             $listing,
        RemoveListingAction $action
    ): RedirectResponse
    {
        $action->execute($listing);

        return back()->with('success', 'Объявление снято с доски.');
    }

}
