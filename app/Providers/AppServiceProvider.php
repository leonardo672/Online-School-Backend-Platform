<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\PaymentDataService;
use Illuminate\Support\ServiceProvider;

use App\Models\User;
use App\Services\UserService;
use App\Services\UserNotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
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

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->singleton(PaymentDataService::class, function ($app) {
            return new PaymentDataService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}