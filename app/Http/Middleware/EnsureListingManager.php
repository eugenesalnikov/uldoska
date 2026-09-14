<?php

namespace App\Http\Middleware;

use App\Models\Listing;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureListingManager
{
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

            $allowed = collect($request->session()->get('manage.listings', []))
                ->map(fn($id) => (int)$id)
                ->push((int)$listing->id)
                ->unique()
                ->values()
                ->all();

            $request->session()->put('manage.listings', $allowed);

            $listing->forceFill([
                'manage_token' => Str::password(32, symbols: false),
            ])->save();

            return redirect()->to($request->url());
        }

        $allowed = collect($request->session()->get('manage.listings', []))
            ->map(fn($id) => (int)$id);

        if ($allowed->contains((int)$listing->id)) {
            return $next($request);
        }

        abort(Response::HTTP_NOT_FOUND);
    }

}
