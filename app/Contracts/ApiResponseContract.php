<?php

namespace App\Contracts;

use App\Enums\HttpStatusCodes;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

interface ApiResponseContract {

    /**
     * @param mixed $data Payload to send to frontend
     * @param string $message Message to send to frontend
     * @param HttpStatusCodes $httpCode HTTP status code
     * @param bool $resetJWT Whether to regenerate and send JWT as cookie
     * @param ?Cookie $cookie Optional cookie to include
     *
     * @return JsonResponse
     */
    public function sendResponse(mixed $data = [], string $message = '', HttpStatusCodes $httpCode = HttpStatusCodes::OK_200, bool $resetJWT = false, ?Cookie $cookie = null): JsonResponse;
}
