<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\PaymentDataService;
use Illuminate\Support\ServiceProvider;

use App\Models\User;
use App\Services\UserService;
use App\Services\UserNotificationService;

// Review Services
use App\Services\Review\ReviewQueryService;
use App\Services\Review\ReviewStatisticsService;
use App\Services\Review\ReviewValidationService;
use App\Services\Review\ReviewExportService;

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

        // Register Review Services
        $this->app->singleton(ReviewQueryService::class, function ($app) {
            return new ReviewQueryService();
        });

        $this->app->singleton(ReviewStatisticsService::class, function ($app) {
            return new ReviewStatisticsService();
        });

        $this->app->singleton(ReviewValidationService::class, function ($app) {
            return new ReviewValidationService();
        });

        $this->app->singleton(ReviewExportService::class, function ($app) {
            return new ReviewExportService();
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