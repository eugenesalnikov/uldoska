<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use App\Services\CurrentDistrict;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class IndexListingController extends Controller
{
    public function __invoke(
        Request         $request,
        CurrentDistrict $currentDistrict,
        ?District       $district = null,
        ?Category       $category = null,
    ): View
    {
        $districts = District::query()
            ->active()
            ->orderBy('sort')
            ->get();

        $categories = Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort')])
            ->orderBy('sort')
            ->get();

        $selectedDistrict = $currentDistrict->resolve($districts, $district);
        $selectedCategory = $category;

        $q = trim((string)$request->query('q', ''));

        $listings = Listing::query()
            ->published()
            ->with(['district', 'category', 'media'])
            ->inDistrict($selectedDistrict)
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $ids = $selectedCategory->isRoot()
                    ? $selectedCategory->children->pluck('id')->push($selectedCategory->id)
                    : collect([$selectedCategory->id]);

                $query->whereIn('category_id', $ids);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('title', 'ilike', '%' . $q . '%')
                        ->orWhere('body', 'ilike', '%' . $q . '%');
                });
            })
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        if ($selectedDistrict && !$selectedCategory) {
            $heading = "Объявления в районе $selectedDistrict->name";
            $title = $heading . ', Ульяновск – Uldoska';
            $description = "Объявления в районе $selectedDistrict->name, Ульяновск. На Uldoska.";
        }

        if ($selectedCategory && !$selectedDistrict) {
            $heading = "$selectedCategory->name в Ульяновске";
            $title = $heading . ' – объявления на Uldoska';
            $description = "Объявления: $selectedCategory->name в Ульяновске. Свежие предложения на Uldoska";
        }

        if ($selectedDistrict && $selectedCategory) {
            $heading = "$selectedCategory->name в районе $selectedDistrict->name";
            $title = $heading . ', Ульяновск – Uldoska';
            $description = "$selectedCategory->name в районе $selectedDistrict->name, Ульяновск. Объявления на Uldoska.";
        }

        if (!$selectedCategory && !$selectedDistrict) {
            $heading = "Объявления в Ульяновске";
            $title = $heading . ', Uldoska';
            $description = "Uldoska – бесплатная доска объявлений Ульяновска.";
        }

        return view('listings.index', [
            'districts'        => $districts,
            'categories'       => $categories,
            'selectedDistrict' => $selectedDistrict,
            'selectedCategory' => $selectedCategory,
            'q'                => $q,
            'listings'         => $listings,
            'heading'          => $heading,
            'title'            => $title,
            'description'      => $description,
        ]);
    }

}
