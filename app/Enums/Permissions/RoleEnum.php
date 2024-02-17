<?php declare(strict_types=1);

namespace App\Enums\Permissions;

use BenSampo\Enum\Enum;

final class RoleEnum extends Enum
{
    const SUPER_ADMIN = 'super_admin';

    const CALL_CENTER_REPRESENTATIVE = 'CSR';
}
