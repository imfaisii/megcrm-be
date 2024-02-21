<?php

declare(strict_types=1);

namespace App\Enums\Events;

use App\Models\Calendar;
use BenSampo\Enum\Enum;

final class SurveyBookedEnum extends Enum
{
    const TITLE = 'Survey Booked';
    const NOTIFICATION_TITLE = 'Survey Booking Notification';
    const CALENDAR_NAME = 'Surveys';
    const IS_FULL_DAY = true;

    public static function getCalendarId(): int|null
    {
        return Calendar::where('name', self::CALENDAR_NAME)->first()?->id;
    }

    public static function getDescriptionMessage(string $name, string $address, string $time)
    {
        return "You have a survey booked at the location : {$address} on {$time} with {$name}.";
    }

    public static function getNotificationSubtitle(string $name, string $time)
    {
        return "You have a survey booked with {$name} at {$time}.";
    }
}
