<?php

namespace App\Enums;

enum ClaimSource : string
{
    const CUSTOMER_PORTAL = 'customer_portal';
    const AGENT_PORTAL    = 'agent_portal';
    const STAFF_PORTAL    = 'staff_portal';

    public static function labels(): array
    {
        return [
            self::CUSTOMER_PORTAL => 'Customer Portal',
            self::AGENT_PORTAL    => 'Agent Portal',
            self::STAFF_PORTAL    => 'Staff Portal',
        ];
    }
}
