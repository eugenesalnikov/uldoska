<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Contracts\View\View;

class ManageListingController extends Controller
{
    public function __invoke(Listing $listing): View
    {
        return view('listings.manage', compact('listing'));
    }

}
