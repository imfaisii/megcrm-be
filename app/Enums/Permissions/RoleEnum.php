<?php

declare(strict_types=1);

namespace App\Enums\Permissions;

use BenSampo\Enum\Enum;

final class RoleEnum extends Enum
{
    const SUPER_ADMIN = 'super_admin';
    const CSR = 'csr';
    const SURVEYOR = 'surveyor';
    const INSTALLER = 'installer';
    const FINANCE = 'finance';
    const HR = 'hr';
    const TRANSPORT = 'transport';
    const SUBMISSION = 'submission';
    const PRECHECK = 'precheck';
}
