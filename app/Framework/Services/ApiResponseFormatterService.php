<?php

namespace App\Framework\Services;

use App\Framework\Enums\HttpStatusCodes;
use App\Framework\Facades\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseFormatterService
{
    /**
     * Check if the request is for an API route
     */
    public function isApiRequest(Request $request): bool
    {
        $path = $request->path();
        // Check if the path starts with version pattern (v1, v2, etc.)
        return preg_match('/^(v\d+)\//', $path);
    }

    /**
     * Map HTTP status code to enum
     */
    public function mapStatusCodeToEnum(int $statusCode): HttpStatusCodes
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
    public function extractMessage(array $data, int $statusCode): string
    {
        // Try to extract message from common response structures
        if (isset($data['message'])) {
            return $data['message'];
        }

        if (isset($data['error'])) {
            return is_string($data['error']) ? $data['error'] : 'An error occurred';
        }

        // Default messages based on status code
        return match ($statusCode) {
            200 => 'Success',
            201 => 'Created successfully',
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
     * Extract data from response (excluding message)
     */
    public function extractDataFromResponse(array $responseData): array
    {
        // Remove message and error keys to get clean data
        return array_filter($responseData, function ($key) {
            return !in_array($key, ['message', 'error', 'errors']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Format response using ApiResponse facade
     */
    public function formatResponse(array $data, string $message, HttpStatusCodes $httpCode): JsonResponse
    {
        return ApiResponse::sendResponse($data, $message, $httpCode);
    }
}
