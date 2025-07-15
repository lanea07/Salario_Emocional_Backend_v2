<?php

namespace App\Framework\Facades;

use App\Framework\Contracts\ApiResponseContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse sendResponse(mixed $data = [], string $message = '', HttpStatusCodes $httpCode = HttpStatusCodes::OK_200, bool $resetJWT = false, ?Cookie $cookie = null)
 */
class ApiResponse extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return ApiResponseContract::class;
    }
}
