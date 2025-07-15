<?php

namespace App\Framework\Contracts;

use App\Framework\Enums\HttpStatusCodes;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

interface ApiResponseContract
{

    /**
     * Send a standardized API response
     * 
     * ⚠️  IMPORTANT FOR DEVELOPERS: 
     * All implementations MUST add the header 'X-API-Formatted: true' to mark 
     * the response as already formatted by our ApiResponse system. This prevents 
     * the FormatApiResponse middleware from double-formatting responses.
     * 
     * Example implementation:
     * ```php
     * $response = new JsonResponse($responseData, $httpCode->value);
     * $response->headers->set('X-API-Formatted', 'true');
     * return $response;
     * ```
     * 
     * @param mixed $data Payload to send to frontend
     * @param string $message Message to send to frontend
     * @param HttpStatusCodes $httpCode HTTP status code
     * @param bool $resetJWT Whether to regenerate and send JWT as cookie
     * @param Cookie|null $cookie Optional cookie to include in response
     * @return JsonResponse Standardized JSON response with X-API-Formatted header
     */
    public function sendResponse(mixed $data = [], string $message = '', HttpStatusCodes $httpCode = HttpStatusCodes::OK_200, bool $resetJWT = false, ?Cookie $cookie = null): JsonResponse;
}
