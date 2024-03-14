<?php

namespace App\Providers;

use App\Classes\AirCall;
use App\Classes\LeadResponseClass;
use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\Gate;
use App\Imports\Leads\LeadsImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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

        Queue::failing(function (JobFailed $event) {
            Log::channel('slack_exceptions')->error("JobFailed " . $event->exception);    // whenever a job gets failed and moved to failed job or pki wali fail yani red wali fail
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::SUPER_ADMIN) ? true : null;
        });

    }
}
