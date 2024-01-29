<?php declare(strict_types=1);

namespace App\Enums\Leads;

use BenSampo\Enum\Enum;

final class StatusEnum extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
}
