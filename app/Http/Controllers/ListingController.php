<?php

namespace App\Http\Controllers;

use App\Actions\ExtendListingAction;
use App\Actions\RemoveListingAction;
use App\Actions\StoreListingAction;
use App\Enums\ListingStatus;
use App\Exceptions\DomainException;
use App\Http\Requests\StoreListingRequest;
use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use App\Services\CurrentDistrict;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use ImagickException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

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

        $heading = $q !== '' ? 'Поиск' : 'Объявления';
        if ($selectedDistrict) {
            $heading .= ' – ' . $selectedDistrict->name;
        }
        if ($selectedCategory) {
            $heading .= ' – ' . $selectedCategory->name;
        }

        $title = $heading . ' – объявления Ульяновска';

        $description = $title;

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

    /**
     * @throws ImagickException
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws DomainException
     */
    public function store(
        StoreListingRequest $request,
        StoreListingAction  $action,
    ): RedirectResponse
    {
        $listing = $action->execute($request->toData());

        $allowed = collect($request->session()->get('manage.listings', []))
            ->map(fn($id) => (int)$id)
            ->push((int)$listing->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put('manage.listings', $allowed);

        return redirect()
            ->route('listings.success')
            ->with('success', 'Объявление принято. Осталось подтвердить его в Telegram.');
    }

    public function success(Request $request): View
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

    public function show(Listing $listing): View|RedirectResponse
    {
        if ($listing->isPublished()) {
            $listing->load(['district', 'category', 'media']);

            $districtName = $listing->district
                ? $listing->district->name
                : 'Весь город';

            $title = $listing->title . ' – ' . $listing->category->name . ' – ' . $districtName . ' – объявления Ульяновска';
            $heading = $listing->title;
            $description = $listing->seoDescription();

            return view('listings.show', [
                'listing'     => $listing,
                'heading'     => $heading,
                'title'       => $title,
                'description' => $description,
            ]);
        }

        if ($listing->statusIn([ListingStatus::Expired, ListingStatus::Removed]) && $listing->category) {
            return redirect()->route('listings.district.category', [
                'category' => $listing->category,
                'district' => $listing->district,
            ], 301);
        }

        abort(404);
    }

    public function manage(Listing $listing): View
    {
        return view('listings.manage', compact('listing'));
    }

    /**
     * @throws DomainException
     */
    public function extend(
        Listing             $listing,
        ExtendListingAction $action
    ): RedirectResponse
    {
        $action->execute($listing);

        return back()->with('success', 'Объявление продлено.');
    }

    /**
     * @throws DomainException
     */
    public function remove(
        Listing             $listing,
        RemoveListingAction $action
    ): RedirectResponse
    {
        $action->execute($listing);

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
