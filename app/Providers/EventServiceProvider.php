<?php

namespace App\Providers;

use App\Events\Calendars\NewCalendarEvent;
use App\Events\LoginUserEvent;
use App\Events\Users\NewUserCreated;
use App\Listeners\Calendars\SendCalendarEventListener;
use App\Listeners\UserLoginEventListener;
use App\Listeners\Users\SendUserCredentialsListener;
use App\Models\CalenderEvent;
use App\Models\InstallationBooking;
use App\Models\Lead;
use App\Models\SurveyBooking;
use App\Models\User;
use App\Observers\Calendars\CalendarEventObserver;
use App\Observers\LeadObserver;
use App\Observers\Leads\InstallationBookingObserver;
use App\Observers\Leads\SurveyBookingObserver;
use App\Observers\Users\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // users events
        NewUserCreated::class => [
            SendUserCredentialsListener::class,
        ],

        // calendar events
        NewCalendarEvent::class => [
            SendCalendarEventListener::class,
        ],

         // calendar events
         LoginUserEvent::class => [
            UserLoginEventListener ::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        SurveyBooking::observe(SurveyBookingObserver::class);
        InstallationBooking::observe(InstallationBookingObserver::class);
        CalenderEvent::observe(CalendarEventObserver::class);
        User::observe(UserObserver::class);
        Lead::observe(LeadObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
