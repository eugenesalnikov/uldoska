<?php

namespace App\Http\Controllers\ModeratorListing;

use App\Actions\Listing\ApproveListingAction;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;

class ModeratorListingApproveController extends Controller
{
    /**
     * @throws DomainException
     */
    public function __invoke(
        Listing              $listing,
        ApproveListingAction $action,
    ): RedirectResponse
    {
        $action->execute($listing);

        return redirect()
            ->route('moderator.index')
            ->with('success', 'Объявление одобрено.');
    }

}
