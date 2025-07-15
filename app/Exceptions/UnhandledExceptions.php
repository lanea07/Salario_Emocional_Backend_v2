<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class UnhandledExceptions extends Handler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Only handle JSON requests for API routes
        if ($request->expectsJson() && $this->isApiRequest($request)) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
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
     * Handle API exceptions and format them using the ApiResponse facade
     */
    public function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        $httpCode = $this->getHttpStatusCode($e);
        $message = $this->getExceptionMessage($e);
        $data = $this->getExceptionData($e);

        return ApiResponse::sendResponse($data, $message, $httpCode);
    }

    /**
     * Get the appropriate HTTP status code for the exception
     */
    private function getHttpStatusCode(Throwable $e): HttpStatusCodes
    {
        return match (true) {
            $e instanceof ValidationException => HttpStatusCodes::UNPROCESSABLE_ENTITY_422,
            $e instanceof NotFoundHttpException => HttpStatusCodes::NOT_FOUND_404,
            $e instanceof MethodNotAllowedHttpException => HttpStatusCodes::METHOD_NOT_ALLOWED_405,
            $e instanceof HttpException => $this->mapHttpStatusCode($e->getStatusCode()),
            default => HttpStatusCodes::INTERNAL_SERVER_ERROR_500,
        };
    }

    /**
     * Map HTTP status codes to enum values
     */
    private function mapHttpStatusCode(int $statusCode): HttpStatusCodes
    {
        return match ($statusCode) {
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
     * Get the exception message
     */
    private function getExceptionMessage(Throwable $e): string
    {
        return match (true) {
            $e instanceof ValidationException => 'Validation failed',
            $e instanceof NotFoundHttpException => 'Resource not found',
            $e instanceof MethodNotAllowedHttpException => 'Method not allowed',
            $e instanceof HttpException => $e->getMessage() ?: 'HTTP error',
            default => config('app.debug') ? $e->getMessage() : 'Internal server error',
        };
    }

    /**
     * Get the exception data
     */
    private function getExceptionData(Throwable $e): array
    {
        return match (true) {
            $e instanceof ValidationException => [
                'errors' => $e->errors(),
            ],
            $e instanceof HttpException && config('app.debug') => [
                'debug' => [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ],
            ],
            default => [],
        };
    }
}
