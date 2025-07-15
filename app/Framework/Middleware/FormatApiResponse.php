<?php

namespace App\Framework\Middleware;

use App\Framework\Services\ApiResponseFormatterService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatApiResponse
{
    public function __construct(
        private ApiResponseFormatterService $formatterService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only handle JSON responses for API routes
        if ($request->expectsJson() && $this->formatterService->isApiRequest($request) && $this->shouldFormatResponse($response)) {
            return $this->formatResponse($response);
        }

        return $response;
    }

    /**
     * Check if the response should be formatted
     */
    private function shouldFormatResponse($response): bool
    {
        // Only format JSON responses
        if (!$response instanceof JsonResponse) {
            return false;
        }

        // Don't format if it's already been processed by our ApiResponse system
        if ($response->headers->has('X-API-Formatted')) {
            return false;
        }

        return true;
    }

    /**
     * Format the response using the shared formatter service
     */
    private function formatResponse(JsonResponse $response): JsonResponse
    {
        $statusCode = $response->getStatusCode();
        $originalData = $response->getData(true);

        $httpCode = $this->formatterService->mapStatusCodeToEnum($statusCode);
        $message = $this->formatterService->extractMessage($originalData, $statusCode);
        $data = $this->formatterService->extractDataFromResponse($originalData);

        return $this->formatterService->formatResponse($data, $message, $httpCode);
    }
}
