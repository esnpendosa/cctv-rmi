<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\CameraRepositoryInterface::class,
            \App\Repositories\CameraRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\InventoryRepositoryInterface::class,
            \App\Repositories\InventoryRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\CameraWentOffline::class,
            \App\Listeners\SendCameraOfflineNotification::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\CameraWentOnline::class,
            \App\Listeners\SendCameraOnlineNotification::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\AuditLogCreated::class,
            \App\Listeners\WriteAuditLog::class
        );

        \Illuminate\Support\Facades\RateLimiter::for('api_standard', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('api_monitoring_live', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}


