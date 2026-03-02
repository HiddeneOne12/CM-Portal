<?php

namespace App\Http\Middleware;

use App\Traits\HasPermissionsTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    use HasPermissionsTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Server-side access check using actual request path (not route pattern)
        $path = $request->path();
        $response = $this->getModulesPremissionsByPath($path);
        if (!$response) {
            $fallback = $this->getModulesPremissionsBySlug('admin/dashboard');
            if ($fallback) {
                return redirect()->route('admin.dashboard');
            }
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
