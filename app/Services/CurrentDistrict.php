<?php

namespace App\Services;

use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

readonly class CurrentDistrict
{
    public function __construct(
        private Request $request,
    )
    {
    }

    public function resolve(Collection $districts, ?District $fromRoute = null): ?District
    {
        if ($fromRoute) {
            $this->remember($fromRoute);

            return $fromRoute;
        }

        /** @var District */
        return $districts->first(
            fn (District $district) => $district->slug === $this->request->cookie('district')
        );
    }

    public function remember(District $district): void
    {
        cookie()->queue('district', $district->slug, 60 * 24 * 365);
    }

    public function forget(): void
    {
        cookie()->queue(cookie()->forget('district'));
    }

}
