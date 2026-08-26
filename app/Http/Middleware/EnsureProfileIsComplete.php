<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->name === null
            && ! $request->routeIs('onboarding.*')
            && ! $request->routeIs('logout')) {
            return redirect()->route('onboarding.name');
        }

        return $next($request);
    }
}
