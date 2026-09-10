<?php

namespace App\Http\Controllers;

use App\Actions\ManageListingAction;
use App\Actions\StoreListingAction;
use App\Enums\ListingStatus;
use App\Http\Requests\StoreListingRequest;
use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use App\Services\CurrentDistrict;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ListingController extends Controller
{
    public function __construct(
        private readonly CurrentDistrict $currentDistrict,
    )
    {
    }

    public function index(
        Request   $request,
        ?District $district = null,
        ?Category $category = null,
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

        $selectedDistrict = $this->currentDistrict->resolve($districts, $district);
        $selectedCategory = $category;

        $q = trim((string)$request->query('q', ''));

        $listings = Listing::query()
            ->published()
            ->with(['district', 'category', 'media'])
            ->when($selectedDistrict, fn($query) => $query->where('district_id', $selectedDistrict->id))
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
            ->paginate(12)
            ->withQueryString();

        return view('listings.index', [
            'districts'        => $districts,
            'categories'       => $categories,
            'selectedDistrict' => $selectedDistrict,
            'selectedCategory' => $selectedCategory,
            'q'                => $q,
            'listings'         => $listings,
            'title'            => $selectedCategory->name ?? ($q !== '' ? 'Поиск' : 'Объявления'),
        ]);
    }

    public function create(): View
    {
        return view('listings.create', [
            'categories' => Category::query()
                ->active()
                ->roots()
                ->with(['children' => fn($q) => $q->active()->orderBy('sort')])
                ->orderBy('sort')
                ->get(),
            'districts'  => District::query()->active()->orderBy('sort')->get(),
        ]);
    }

    public function store(
        StoreListingRequest $request,
        StoreListingAction  $action,
    ): RedirectResponse
    {
        $listing = $action->execute($request);

        return redirect()
            ->route('listings.success', $listing)
            ->with('success', 'Объявление принято. Осталось подтвердить его в Telegram.');
    }

    public function success(Listing $listing): View
    {
        return view('listings.success', [
            'listing' => $listing,
        ]);
    }

    public function show(Listing $listing): View
    {
        abort_unless($listing->isPublished(), 404);

        $listing->load(['district', 'category', 'media']);

        return view('listings.show', compact('listing'));
    }

    public function manage(Listing $listing): View
    {
        return view('listings.manage', compact('listing'));
    }

    public function extend(
        Listing             $listing,
        ManageListingAction $action
    ): RedirectResponse
    {
        try {
            $action->extend($listing);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Объявление продлено.');
    }

    public function remove(
        Listing $listing,
        ManageListingAction $action
    ): RedirectResponse
    {
        $action->remove($listing);

        return back()->with('success', 'Объявление снято с доски.');
    }

    public function go(Request $request): RedirectResponse
    {
        $district = $request->query('district');
        $category = $request->query('category');
        $q = trim((string)$request->query('q', ''));
        $params = array_filter(['q' => $q !== '' ? $q : null]);

        if ($request->query->has('district') && !$district) {
            $this->currentDistrict->forget();
            $district = null;
        }

        if ($district && $category) {
            return redirect()->route('listings.district.category', [
                'district' => $district,
                'category' => $category,
                ...$params,
            ]);
        }

        if ($district) {
            return redirect()->route('listings.district', [
                'district' => $district,
                ...$params,
            ]);
        }

        if ($category) {
            return redirect()->route('listings.category', [
                'category' => $category,
                ...$params,
            ]);
        }

        return redirect()->route('listings.index', $params);
    }

}
