<?php

namespace App\Services;

use App\Traits\HasPermissionsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Centralized server-side access control.
 * Use this (or validatePermissions helper) to enforce permissions on every request.
 */
class AccessService
{
    use HasPermissionsTrait;

    /**
     * Check if the current admin user can access the given path.
     * Path is normalized (e.g. "acl/events" -> "admin/acl/events").
     */
    public function canAccessPath(string $path): bool
    {
        if (! Auth::guard('admin')->check()) {
            return false;
        }

        return $this->getModulesPremissionsByPath($path) !== null;
    }

    /**
     * Check if the current admin user has permission for the given slug.
     * Slug format: "admin/acl/events", "admin/acl/events/edit", etc.
     */
    public function canAccessSlug(string $slug): bool
    {
        return HasPermissionsTrait::getModulesPremissionsBySlug($slug);
    }

    /**
     * Check access for the current request (uses request path).
     */
    public function canAccessRequest(Request $request): bool
    {
        return $this->canAccessPath($request->path());
    }
}
