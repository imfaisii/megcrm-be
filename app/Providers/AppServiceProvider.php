<?php

namespace App\Providers;

use App\Notifications\TestNotification;
use Illuminate\Support\ServiceProvider;
use Test;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
        app()->bind(TestNotification::class, function ($app, $parameters) {
            return new TestNotification(...$parameters);
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
