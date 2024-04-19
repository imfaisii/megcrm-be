<?php

declare(strict_types=1);

namespace App\Enums\Users;

use BenSampo\Enum\Enum;

final class MediaCollectionEnum extends Enum
{
    const DOCUMENTS = 'documents';

    const COMPANY_DOCUMENTS = 'company-documents';

    const SUBMISSION_DOCUMENTS = 'submission_documents';
}
