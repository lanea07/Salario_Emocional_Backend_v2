<?php

namespace App\Framework\Exceptions;

use App\Framework\Enums\HttpStatusCodes;
use App\Framework\Services\ApiResponseFormatterService;
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
    private ApiResponseFormatterService $formatterService;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->formatterService = app(ApiResponseFormatterService::class);
    }

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
        if ($request->expectsJson() && $this->formatterService->isApiRequest($request)) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions and format them using the shared formatter service
     */
    public function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        $httpCode = $this->getHttpStatusCode($e);
        $message = $this->getExceptionMessage($e);
        $data = $this->getExceptionData($e);

        return $this->formatterService->formatResponse($data, $message, $httpCode);
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
            $e instanceof HttpException => $this->formatterService->mapStatusCodeToEnum($e->getStatusCode()),
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
