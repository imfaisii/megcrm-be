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

    const CUSTOMER_LEAD_IMAGES = 'customer_survey_images';

    const CUSTOMER_LEAD_DOCUMENTS = 'customer_lead_images';

    const Default_MediaType = 'meg-crm-default-collection';

    public static function CustomerLeadCollectionsList(): array
    {
        return [
            self::CUSTOMER_LEAD_IMAGES,
            self::CUSTOMER_LEAD_DOCUMENTS,
        ];
    }

}
