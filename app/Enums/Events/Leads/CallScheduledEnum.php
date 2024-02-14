<?php

declare(strict_types=1);

namespace App\Enums\Events\Leads;

use App\Models\Calendar;
use BenSampo\Enum\Enum;

final class CallScheduledEnum extends Enum
{
    const CALENDAR_NAME = 'Scheduled Calls';
    const TITLE = 'Call Scheduled';
    const IS_FULL_DAY = false;

    public static function getCalendarId(): int|null
    {
        return Calendar::where('name', self::CALENDAR_NAME)->first()?->id;
    }

    public static function getDescriptionMessage(string $customerName, string $leadStatus): string
    {
        return "You have a scheduled call with {$customerName} with a lead status of {$leadStatus}";
    }
}
