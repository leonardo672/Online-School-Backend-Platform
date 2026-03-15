<?php
// app/Providers/ServiceServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Services\UserService;
use App\Services\UserNotificationService;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register UserService
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app->make(User::class));
        });

        // Register UserNotificationService
        $this->app->singleton(UserNotificationService::class, function ($app) {
            return new UserNotificationService();
        });
    }

    public function boot(): void
    {
        //
    }
}