<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenActionException;
use App\Traits\ValidatesPermissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateActions {

    use ValidatesPermissions;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $route = $request->route();

        if(!isset($route?->defaults['permissions']) || !count($route?->defaults['permissions'])) throw new ForbiddenActionException();

        $permissions = $route?->defaults['permissions'];
        $permissions = array_map('intval', $permissions);
        self::hasPermissions($permissions, true);
        return $next($request);
    }
}
