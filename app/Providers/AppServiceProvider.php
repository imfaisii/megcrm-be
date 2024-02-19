<?php

namespace App\Providers;

use App\Classes\AirCall;
use App\Notifications\Events\NewCalLScheduledNotification;
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

        app()->bind(NewCalLScheduledNotification::class, function ($app, $parameters) {
            return new NewCalLScheduledNotification(...$parameters);
        });

        $this->app->bind('AirCall', function () {
            return new AirCall();
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
