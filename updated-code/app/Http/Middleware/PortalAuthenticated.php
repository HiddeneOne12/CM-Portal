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

    // 🔐 IP binding check
    if (session('portal_ip') !== $request->ip()) {
        $request->session()->invalidate();
        return redirect()->route('members-portal')
            ->with('error', 'Session security validation failed.');
    }

    // 🔐 User-agent binding check
    if (session('portal_user_agent') !== substr($request->userAgent(), 0, 255)) {
        $request->session()->invalidate();
        return redirect()->route('members-portal')
            ->with('error', 'Session security validation failed.');
    }

    return $next($request);
}
}
