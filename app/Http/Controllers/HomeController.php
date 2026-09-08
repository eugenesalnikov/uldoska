<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use App\Services\CurrentDistrict;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(
        private readonly CurrentDistrict $currentDistrict,
    )
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request   $request,
        ?District $district = null
    ): View
    {
        $districts = District::query()
            ->active()
            ->orderBy('sort')
            ->get();

        $selectedDistrict = $this->currentDistrict->resolve($districts, $district);

        $categories = Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort')])
            ->orderBy('sort')
            ->get();

        $fresh = Listing::query()
            ->published()
            ->with(['district', 'category', 'media'])
            ->when($selectedDistrict, fn($q) => $q->where('district_id', $selectedDistrict->id))
            ->latest('published_at')
            ->limit(12)
            ->get();

        return view('home', [
            'districts'        => $districts,
            'selectedDistrict' => $selectedDistrict,
            'categories'       => $categories,
            'fresh'            => $fresh,
        ]);
    }

}
