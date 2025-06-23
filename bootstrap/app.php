<?php

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateActions;
use App\Http\Middleware\ValidateApiVersion;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'setLocale' => SetLocale::class,
            'jwt' => JwtMiddleware::class,
            'hasActions' => ValidateActions::class,
            'validateApiVersion' => ValidateApiVersion::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
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
