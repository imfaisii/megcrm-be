<?php

namespace App\Enums\AirCall;

use BenSampo\Enum\Enum;

final class AirCallEnum extends Enum
{
    const USER_RESOURCE = 'user';

    const NUMBER_RESOURCE = 'number';

    const CALL_RESOURCE = 'call';

    const CONTACT_RESOURCE = 'contact';

    // Number Event
    const NUMBER_CREATED = 'number_created';

    const NUMBER_OPENED = 'number_opened';

    const NUMBER_CLOSED = 'number_closed';

    const NUMBER_DELETED = 'number_deleted';

    // Contanct Event

    const CONTACT_CREATED = 'contact_created';

    const CONTACT_UPDATED = 'contact_updated';

    const CONTACT_DELETED = 'contact_deleted';

    // Call Event

    const CALL_CREATED = 'call_created';

    const CALL_ANSWERED = 'call_answered';

    const CALL_HUNGUP = 'call_hungup';

    const CALL_ENDED = 'call_ended';

    const CALL_ASSIGNED = 'call_assigned';

    const CALL_ARCHIVED = 'call_archived';

    const CALL_TAGGED = 'call_tagged';

    const CALL_UNTAGGED = 'call_untagged';

    const CALL_COMMENTED = 'call_commented';

    const CALL_TRANSFERRED = 'call_transferred';

    const CALL_UNSUCCESSFUL_TRANSFER = 'call_unsuccessful_transfer';

    const CALL_RINGING_ON_AGENT = 'call_ringing_on_agent';

    const CALL_AGENT_DECLINED = 'call_agent_declined';

    const CALL_VOICEMAIL_LEFT = 'call_voicemail_left';

    const CALL_HOLD = 'call_hold';

    const CALL_UNHOLD = 'call_unhold';

    // User Event

    const USER_CREATED = 'user_created';

    const USER_OPENED = 'user_opened';

    const USER_CLOSED = 'user_closed';

    const USER_DELETED = 'user_deleted';

    const USER_CONNECTED = 'user_connected';

    const USER_DISCONNECTED = 'user_disconnected';

    const USER_WUT_START = 'user_wut_start';

    const USER_WUT_END = 'user_wut_end';

    public function getNumberEvents(): array
    {
        return [
            self::NUMBER_CREATED,
            self::NUMBER_OPENED,
            self::NUMBER_CLOSED,
            self::NUMBER_DELETED,
        ];
    }

    public function getContactEvents(): array
    {
        return [
            self::CONTACT_CREATED,
            self::CONTACT_UPDATED,
            self::CONTACT_DELETED,
        ];
    }

    public function getUserEvents(): array
    {
        return [
            self::USER_CREATED,
            self::USER_OPENED,
            self::USER_CLOSED,
            self::USER_DELETED,
            self::USER_CONNECTED,
            self::USER_DISCONNECTED,
            self::USER_WUT_START,
            self::USER_WUT_END,
        ];
    }

    public function getCallEvents(): array
    {
        return [
            self::CALL_CREATED,
            self::CALL_ANSWERED,
            self::CALL_HUNGUP,
            self::CALL_ENDED,
            self::CALL_ASSIGNED,
            self::CALL_ARCHIVED,
            self::CALL_TAGGED,
            self::CALL_UNTAGGED,
            self::CALL_COMMENTED,
            self::CALL_TRANSFERRED,
            self::CALL_UNSUCCESSFUL_TRANSFER,
            self::CALL_RINGING_ON_AGENT,
            self::CALL_AGENT_DECLINED,
            self::CALL_VOICEMAIL_LEFT,
            self::CALL_HOLD,
            self::CALL_UNHOLD,
        ];
    }

    public function getResources(): array
    {
        return [
            self::USER_RESOURCE,
            self::NUMBER_RESOURCE,
            self::CONTACT_RESOURCE,
            self::CALL_RESOURCE,
        ];
    }
}
