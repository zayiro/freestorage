<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CashierService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CashierService::class, function ($app) {
            return new CashierService();
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
