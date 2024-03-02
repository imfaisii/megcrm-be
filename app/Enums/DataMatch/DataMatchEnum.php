<?php


namespace App\Enums\DataMatch;

use BenSampo\Enum\Enum;

final class DataMatchEnum extends Enum
{
    /* Before Data Match */
    const StatusSent = "Sent";
    const StatusReceived = "Received";

    const StatusNotSent = "Not Sent";

    /* After Data Match Result */
    const PLEASE_SELECT = 'Please Select';
    const NOT_SENT_YET = 'Not Sent Yet';
    const REQUIRED_DATAMATCH = 'Required Datamatch';
    const SENT = 'Sent';
    const MATCHED = 'Matched';
    const NOT_MATCHED = 'Not Matched';
    const VERIFIED = 'Verified';
    const UNMATCHED_VERIFIED = 'Unmatched (verified)';
    const UNVERIFIED = 'Unverified';

}
