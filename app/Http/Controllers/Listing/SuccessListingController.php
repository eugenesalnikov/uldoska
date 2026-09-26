<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SuccessListingController extends Controller
{
    public function __invoke(Request $request): View
    {
        $ids = collect($request->session()->get('manage.listings', []))
            ->map(fn($id) => (int)$id);

        abort_if($ids->isEmpty(), 404);

        $listing = Listing::query()
            ->whereIn('id', $ids)
            ->latest('id')
            ->first();

        abort_if(!$listing, 404);

        return view('listings.success', [
            'listing' => $listing,
        ]);
    }

}
