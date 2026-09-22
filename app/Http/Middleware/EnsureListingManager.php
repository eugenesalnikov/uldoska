<?php

namespace App\Http\Middleware;

use App\Models\Listing;
use App\Services\ManagedListings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureListingManager
{
    public function __construct(
        private ManagedListings $managedListings,
    )
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $listing = $request->route('listing');

        if (!$listing instanceof Listing) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($request->query->has('key')) {
            $token = (string)$request->query('key');

            if (
                $token === ''
                || !is_string($listing->manage_token)
                || !hash_equals($listing->manage_token, $token)
            ) {
                abort(Response::HTTP_NOT_FOUND);
            }

            $this->managedListings->allow($listing);

            $listing->forceFill([
                'manage_token' => Str::password(32, symbols: false),
            ])->save();

            return redirect()->to($request->url());
        }

        if ($this->managedListings->contains($listing)) {
            return $next($request);
        }

        abort(Response::HTTP_NOT_FOUND);
    }

}
