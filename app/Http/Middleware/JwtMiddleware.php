<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Http\Request;

class JwtMiddleware {
    
    public function handle(Request $request, Closure $next) {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return ApiResponse::sendResponse(message: __('auth.jwt_invalid_unauthorized'), httpCode: HttpStatusCodes::UNAUTHORIZED_401);
        }
        return $next($request);
    }
}
