<?php

namespace App\Framework\Middleware;

use App\Framework\Traits\ValidatesPermissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateActions
{

    use ValidatesPermissions;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        $endSession = $route?->defaults['endSession'] ?? true;
        $permissions = $route?->defaults['permissions'] ?? [];
        $permissions = array_map('intval', $permissions);
        self::hasPermissions($permissions, $endSession);
        return $next($request);
    }
}
