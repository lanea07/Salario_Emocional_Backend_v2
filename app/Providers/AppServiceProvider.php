<?php

namespace App\Providers;

use App\Services\AdminService;
use App\Services\AuthService;
use App\Services\BenefitDetailService;
use App\Services\BenefitService;
use App\Services\BenefitUserService;
use App\Services\DependencyService;
use App\Services\PositionService;
use App\Services\PreferencesService;
use App\Services\RoleService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            throw new Exception('An error has occurred:' . $e);
        }

        // Services
        $this->app->singleton(AdminService::class);
        $this->app->singleton(AuthService::class);
        $this->app->singleton(BenefitDetailService::class);
        $this->app->singleton(BenefitService::class);
        $this->app->singleton(BenefitUserService::class);
        $this->app->singleton(DependencyService::class);
        $this->app->singleton(PositionService::class);
        $this->app->singleton(PreferencesService::class);
        $this->app->singleton(RoleService::class);
        $this->app->singleton(UserService::class);
    }
}
