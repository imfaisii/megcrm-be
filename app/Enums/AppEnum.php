<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AppEnum extends Enum
{
    const MailQue = 'mail-que';
    const SlackQue = 'slack-que';

    public static function QueueOptions(): array
    {
        return [
            self::MailQue,
            self::SlackQue,
        ];
    }
}
