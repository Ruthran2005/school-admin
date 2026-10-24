<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModuleAccessMiddleware
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $user = $request->user();
        $allowed = $module === 'admins'
            ? $user?->canManageAdmins()
            : $user?->hasModuleAccess($module);

        abort_unless($allowed, 403, 'You are not allowed to access this module.');

        return $next($request);
    }
}
