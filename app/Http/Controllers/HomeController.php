<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $districts = District::query()
            ->active()
            ->orderBy('sort')
            ->get();

        $selectedDistrict = $districts->firstWhere(
            'slug',
            $request->query('district', $request->cookie('district'))
        );

        $categories = Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort')])
            ->orderBy('sort')
            ->get();

        $fresh = Listing::query()
            ->published()
            ->with(['district', 'category'])
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
