<?php

namespace App\Facades;

use App\Contracts\ApiResponseContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static JsonResponse sendResponse(mixed $data = [], string $message = '', HttpStatusCodes $httpCode = HttpStatusCodes::OK_200, bool $resetJWT = false, ?Cookie $cookie = null)
 */
class ApiResponse extends Facade {
    
    protected static function getFacadeAccessor(): string {
        return ApiResponseContract::class;
    }
}
