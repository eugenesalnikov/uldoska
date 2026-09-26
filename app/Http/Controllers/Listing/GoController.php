<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Services\CurrentDistrict;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoController extends Controller
{
    public function __invoke(
        Request         $request,
        CurrentDistrict $currentDistrict,
    ): RedirectResponse
    {
        $district = $request->query('district');
        $category = $request->query('category');
        $q = trim((string)$request->query('q', ''));
        $params = array_filter(['q' => $q !== '' ? $q : null]);

        if ($request->query->has('district') && !$district) {
            $currentDistrict->forget();
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
