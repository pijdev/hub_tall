<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, __('Você não tem permissão para acessar esta página.'));
        }

        // Super Admin bypasses all permission checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->hasAnyPermission(...$permissions)) {
            return $next($request);
        }

        abort(403, __('Você não tem permissão para acessar esta página.'));
    }
}
