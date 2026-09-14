<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModerator
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasAccess = $request->session()->get('moderator') === true;

        if (
            ! $hasAccess
            && hash_equals(
                (string) config('uldoska.moderator_key'),
                (string) $request->query('key')
            )
        ) {
            $request->session()->put('moderator', true);
            $hasAccess = true;
        }

        if (! $hasAccess) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($request->query->has('key')) {
            return redirect()->to($request->url());
        }

        return $next($request);
    }

}
