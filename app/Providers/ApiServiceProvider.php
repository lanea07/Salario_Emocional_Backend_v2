<?php

namespace App\Providers;

use App\Contracts\ApiResponseContract;
use App\Factories\ApiResponseFactory;
use App\Services\TempTableService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('tempTable', function () {
            return new TempTableService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $request = request()->path();
        $this->app->bind(ApiResponseContract::class, function () use ($request) {
            return ApiResponseFactory::make($request);
        });
        App::setLocale(env('APP_LOCALE', 'es'));
    }
}
