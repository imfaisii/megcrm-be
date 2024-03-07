<?php

namespace App\Providers;

use App\Classes\AirCall;
use App\Classes\LeadResponseClass;
use App\Imports\Leads\LeadsImport;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        app()->bind(LeadResponseClass::class, function ($app, $parameters) {
            return new LeadResponseClass();
        });

        app()->bind(LeadsImport::class, function ($app, $parameters) {
            return new LeadsImport(new LeadResponseClass());
        });

        $this->app->bind(AirCall::class, function () {
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
