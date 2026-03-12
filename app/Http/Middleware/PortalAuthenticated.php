<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class PortalAuthenticated
{
    public function handle($request, Closure $next)
{
    if (!session('portal_authenticated')) {
        return redirect()->route('members-portal')
            ->with('error', 'Please sign in to access the portal.');
    }

    return $next($request);
}
}
