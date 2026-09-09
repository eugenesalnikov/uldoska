<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModerator
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('moderator') === true) {
            return $next($request);
        }

        if (hash_equals(
            (string)config('uldoska.moderator_key'),
            (string)$request->query('key')
        )) {
            $request->session()->put('moderator', true);

            return redirect()->to($request->url());
        }

        abort(Response::HTTP_NOT_FOUND);
    }

}
