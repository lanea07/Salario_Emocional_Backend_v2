<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale {
    
    public function handle(Request $request, Closure $next): Response {
        $locale = $request->query('lang');

        if (!in_array($locale, ['en', 'es'])) {
            $locale = config('app.fallback_locale');
        }

        App::setLocale($locale);
        return $next($request);
    }
}
