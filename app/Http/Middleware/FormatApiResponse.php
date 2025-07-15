<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only handle JSON responses for API routes
        if ($request->expectsJson() && $this->isApiRequest($request) && $this->shouldFormatResponse($response)) {
            return $this->formatResponse($response);
        }

        return $response;
    }

    /**
     * Check if the request is for an API route
     */
    private function isApiRequest(Request $request): bool
    {
        $path = $request->path();
        // Check if the path starts with version pattern (v1, v2, etc.)
        return preg_match('/^(v\d+)\//', $path);
    }

    /**
     * Check if the response should be formatted
     */
    private function shouldFormatResponse($response): bool
    {
        // Only format JSON responses that don't already use our custom format
        if (!$response instanceof JsonResponse) {
            return false;
        }

        $data = $response->getData(true);

        // Don't format if it's already our custom format (v1 has 'data' and 'message', v2 has 'meta' and 'payload')
        if ((isset($data['data']) && isset($data['message'])) ||
            (isset($data['meta']) && isset($data['payload']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Format the response using ApiResponse facade
     */
    private function formatResponse(JsonResponse $response): JsonResponse
    {
        $statusCode = $response->getStatusCode();
        $originalData = $response->getData(true);

        $httpCode = $this->mapStatusCodeToEnum($statusCode);
        $message = $this->getMessageFromResponse($originalData, $statusCode);
        $data = $this->getDataFromResponse($originalData);

        return ApiResponse::sendResponse($data, $message, $httpCode);
    }

    /**
     * Map HTTP status code to enum
     */
    private function mapStatusCodeToEnum(int $statusCode): HttpStatusCodes
    {
        return match ($statusCode) {
            200 => HttpStatusCodes::OK_200,
            201 => HttpStatusCodes::CREATED_201,
            400 => HttpStatusCodes::BAD_REQUEST_400,
            401 => HttpStatusCodes::UNAUTHORIZED_401,
            403 => HttpStatusCodes::FORBIDDEN_403,
            404 => HttpStatusCodes::NOT_FOUND_404,
            405 => HttpStatusCodes::METHOD_NOT_ALLOWED_405,
            422 => HttpStatusCodes::UNPROCESSABLE_ENTITY_422,
            500 => HttpStatusCodes::INTERNAL_SERVER_ERROR_500,
            default => HttpStatusCodes::INTERNAL_SERVER_ERROR_500,
        };
    }

    /**
     * Extract message from response data
     */
    private function getMessageFromResponse(array $data, int $statusCode): string
    {
        // Check for common Laravel error response patterns
        if (isset($data['message'])) {
            return $data['message'];
        }

        // Default messages based on status code
        return match ($statusCode) {
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            405 => 'Method not allowed',
            422 => 'Validation failed',
            500 => 'Internal server error',
            default => 'Request processed',
        };
    }

    /**
     * Extract data from response
     */
    private function getDataFromResponse(array $originalData): array
    {
        // For validation errors, preserve the errors structure
        if (isset($originalData['errors'])) {
            return ['errors' => $originalData['errors']];
        }

        // For other Laravel responses, include all data except the message
        $data = $originalData;
        unset($data['message']);

        return $data ?: [];
    }
}
