<?php

namespace App\Http\Controllers;

use App\Actions\Listing\ExtendListingAction;
use App\Actions\Listing\RemoveListingAction;
use App\Actions\Listing\StoreListingAction;
use App\Enums\ListingStatus;
use App\Enums\PendingPhotoStatus;
use App\Exceptions\DomainException;
use App\Http\Requests\StoreListingRequest;
use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use App\Models\PendingPhoto;
use App\Services\CurrentDistrict;
use App\Services\ManagedListings;
use App\Services\PhotoOwnerToken;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

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

    public function create(
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

    /**
     * @throws Throwable
     */
    public function store(
        StoreListingRequest $request,
        StoreListingAction  $action,
        PhotoOwnerToken     $ownerToken,
        ManagedListings     $managedListings,
    ): RedirectResponse
    {
        $listing = $action->execute(
            $request->toData($ownerToken->get()),
        );

        $managedListings->allow($listing);

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
                ? $listing->district->name . ', Ульяновск'
                : 'Ульяновск';

            $heading = $listing->title;
            $title = "$listing->title – {$listing->category->name}, $districtName | Uldoska";
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
     * @throws DomainException|Throwable
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
     * @throws DomainException|Throwable
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
