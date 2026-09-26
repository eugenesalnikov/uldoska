<?php

namespace App\Http\Controllers\ModeratorListing;

use App\Actions\Listing\RejectListingAction;
use App\Enums\ListingRejectionReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectListingRequest;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;

class ModeratorListingRejectController extends Controller
{
    public function __invoke(
        RejectListingRequest $request,
        Listing              $listing,
        RejectListingAction  $action,
    ): RedirectResponse
    {
        $action->execute(
            listing: $listing,
            reason: $request->enum('rejection_reason', ListingRejectionReason::class),
            comment: $request->validated('rejection_comment'),
        );

        return redirect()->route('moderator.index')->with('success', 'Отклонено.');
    }

}
