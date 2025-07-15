<?php

namespace App\Framework\Exceptions;

use App\Framework\Enums\HttpStatusCodes;
use App\Framework\Facades\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ForbiddenActionException extends Exception
{

    public function render(): JsonResponse
    {
        return ApiResponse::sendResponse(message: __('messages.forbidden__insufficient_permissions'), httpCode: HttpStatusCodes::FORBIDDEN_403, resetJWT: true);
    }
}
