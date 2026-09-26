<?php

namespace App\Http\Controllers\Listing;

use App\Enums\PendingPhotoStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\PendingPhoto;
use App\Services\PhotoOwnerToken;
use Illuminate\Contracts\View\View;

class CreateListingController extends Controller
{
    public function __invoke(
        PhotoOwnerToken $ownerToken,
    ): View
    {
        $categories = Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort')])
            ->orderBy('sort')
            ->get();

        $districts = District::query()->active()->orderBy('sort')->get();

        $pendingPhotos = PendingPhoto::query()
            ->where('owner_token', $ownerToken->get())
            ->where('status', PendingPhotoStatus::Uploaded)
            ->latest()
            ->get(['uuid']);

        return view('listings.create', [
            'categories'    => $categories,
            'districts'     => $districts,
            'pendingPhotos' => $pendingPhotos,
        ]);
    }

}
