<?php

namespace App\Observers\Leads;

use App\Enums\Events\InstallationBookedEnum;
use App\Models\CalenderEvent;
use App\Models\InstallationBooking;
use Carbon\Carbon;

class InstallationBookingObserver
{
    public function notification(InstallationBooking $installationBooking)
    {
        $updatedProperties = $installationBooking->getDirty();

        if (
            array_key_exists('installer_id', $updatedProperties)
            || array_key_exists('installation_at', $updatedProperties)
        ) {

            if ($installationBooking->installation_at && $installationBooking->installer_id) {

                $installationBooking->lead->setStatus('Installation Booked', 'Assigned by system.');

                $installationBooking->load(['installer', 'lead']);

                $title = InstallationBookedEnum::TITLE.' with '.$installationBooking->lead->full_name;
                $installationAt = $installationBooking->installation_at;

                CalenderEvent::updateOrCreate(
                    [
                        'user_id' => $installationBooking->installer->id,
                        'calendar_id' => InstallationBookedEnum::getCalendarId(),
                        'eventable_type' => InstallationBooking::class,
                        'eventable_id' => $installationBooking->id,
                    ],
                    [
                        'title' => $title,
                        'start_date' => Carbon::parse($installationAt),
                        'end_date' => Carbon::parse($installationAt)->addHours(),
                        'all_day' => InstallationBookedEnum::IS_FULL_DAY,
                        'description' => InstallationBookedEnum::getDescriptionMessage($installationBooking->lead->full_name, $installationBooking->lead->address, Carbon::parse($installationAt)->format(config('app.date_time_format'))),
                        'notification' => [
                            'title' => InstallationBookedEnum::NOTIFICATION_TITLE,
                            'subtitle' => InstallationBookedEnum::getDescriptionMessage($installationBooking->lead->full_name, $installationBooking->lead->address, Carbon::parse($installationAt)->format(config('app.date_time_format'))),
                            'module' => 'installations',
                            'link' => '/calendar',
                        ],
                        'created_by_id' => auth()->user(),
                    ]
                );
            }
        }
    }

    public function created(InstallationBooking $installationBooking): void
    {
        $this->notification($installationBooking);
    }

    public function updated(InstallationBooking $installationBooking): void
    {
        $this->notification($installationBooking);
    }
}
