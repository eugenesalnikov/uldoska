<?php

namespace App\Http\Controllers;

use App\Actions\PublishListingAction;
use App\Actions\RejectListingAction;
use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ModeratorController extends Controller
{
    public function index(): View
    {
        return view('moderator.index', [
            'listings' => Listing::query()
                ->where('status', ListingStatus::Review)
                ->with(['district', 'category'])
                ->latest()
                ->get(),
        ]);
    }

    public function show(Listing $listing): View
    {
        abort_unless($listing->isReview(), 404);
        $listing->load(['district', 'category', 'media']);

        return view('moderator.show', compact('listing'));
    }

    public function publish(
        Listing              $listing,
        PublishListingAction $action,
    ): RedirectResponse
    {
        $action->execute($listing);

        return redirect()->route('moderator.index')->with('success', 'Опубликовано.');
    }

    public function reject(
        Listing             $listing,
        RejectListingAction $action,
    ): RedirectResponse
    {
        $action->execute($listing);

        return redirect()->route('moderator.index')->with('success', 'Отклонено.');
    }

}
