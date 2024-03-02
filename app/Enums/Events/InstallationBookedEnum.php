<?php

declare(strict_types=1);

namespace App\Enums\Events;

use App\Models\Calendar;
use BenSampo\Enum\Enum;

final class InstallationBookedEnum extends Enum
{
    const TITLE = 'Installation Booked';

    const NOTIFICATION_TITLE = 'Installation Booking Notification';

    const CALENDAR_NAME = 'Installations';

    const IS_FULL_DAY = true;

    public static function getCalendarId(): ?int
    {
        return Calendar::where('name', self::CALENDAR_NAME)->first()?->id;
    }

    public static function getDescriptionMessage(string $name, string $address, string $time)
    {
        return "You have an installation booked at the location : {$address} on {$time} with {$name}.";
    }

    public static function getNotificationSubtitle(string $name, string $time)
    {
        return "You have an installation booked with {$name} at {$time}.";
    }
}
