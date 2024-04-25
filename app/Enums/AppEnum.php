<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AppEnum extends Enum
{
    const MAIL_QUE = 'mail-que';

    const SLACK_QUE = 'slack-que';

    public static function QueueOptions(): array
    {
        return [
            self::MAIL_QUE,
            self::SLACK_QUE,
        ];
    }

    /* MEDIA COLLECTIONS */
    const DEFAULT_MEDIA_DELETED_LOCATION = 'MegDeletedDocs';

    const DEFAULT_LIMIT_FOR_MEDIA_FILE_CUSTOMER = 5;

    const DEFAULT_LIMIT_FOR_SUPPORTING_DOCUMENTS_FILE_CUSTOMER = 2;

    const LEAD_TRACKNG_DAYS_ALLOWED = 14;

    const CUSTOMER_LEAD_IMAGES = 'customer_survey_images';

    const CUSTOMER_LEAD_DOCUMENTS = 'customer_lead_documents';

    const Default_MediaType = 'meg-crm-default-collection';

    public static function CustomerLeadCollectionsList(): array
    {
        return [
            self::CUSTOMER_LEAD_IMAGES,
            self::CUSTOMER_LEAD_DOCUMENTS,
        ];
    }

    public static function customerSupportEmails(): array
    {
        return [
            'sabkaur.meg@gmail.com',
            'haamzaaay@gmail.com'
        ];
    }

    /* DataMatch */
    const TEMPLATE_PATH = 'DataMatch-Template/Export/Template.xlsx';
}
