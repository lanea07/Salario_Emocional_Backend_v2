<?php

namespace App\Framework\Providers;

use App\Framework\Contracts\ApiResponseContract;
use App\Framework\Factories\ApiResponseFactory;
use App\Framework\Services\TempTableService;
use Illuminate\Support\ServiceProvider;

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
    }
}
