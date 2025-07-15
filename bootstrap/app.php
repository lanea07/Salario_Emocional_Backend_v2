<?php

use App\Enums\CarbonBoundariesEnum;
use App\Enums\CarbonTimePeriodsEnum;
use App\Framework\Exceptions\UnhandledExceptions;
use App\Framework\Middleware\FormatApiResponse;
use App\Framework\Middleware\JwtMiddleware;
use App\Framework\Middleware\SetLocale;
use App\Framework\Middleware\ValidateActions;
use App\Framework\Middleware\ValidateApiVersion;
use App\Models\Cron;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt' => JwtMiddleware::class,
            'hasActions' => ValidateActions::class,
        ]);

        // Apply middleware to all API routes
        $middleware->api(append: [
            SetLocale::class,
            ValidateApiVersion::class,
            FormatApiResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            // Only handle JSON requests for API routes
            if ($request->expectsJson() && preg_match('/^api\/(v\d+)\//', $request->path())) {
                $handler = new UnhandledExceptions(app());
                return $handler->handleApiException($request, $e);
            }
            return null; // Let default handler take over
        });
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('birthday:check')->everyMinute()->when(function () {
            $initialDate = new Carbon();
            return Cron::shouldIRun('birthday:check', CarbonTimePeriodsEnum::addMonths, 1, CarbonBoundariesEnum::startOfMonth, $initialDate->toDateString(), 8);
        });
        $schedule->command('sanctum:prune-expired --hours=24')->when(function () {
            $initialDate = new Carbon();
            return Cron::shouldIRun('sanctum:prune-expired --hours=24', CarbonTimePeriodsEnum::addDays, 1, CarbonBoundariesEnum::startOfDay, $initialDate->toDateString());
        });
        $schedule->command('reject-old-benefit-requests')->when(function () {
            $initialDate = new Carbon();
            return Cron::shouldIRun('reject-old-benefit-requests', CarbonTimePeriodsEnum::addDays, 1, CarbonBoundariesEnum::startOfDay, $initialDate->toDateString(), 0);
        });
    })
    ->create();
