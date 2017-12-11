<?php

namespace Cocktales\Domain\Session\Hydration;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    public static function fromRawData(\stdClass $data): SessionToken
    {
        return new SessionToken(
            Uuid::createFromBinary($data->token),
            Uuid::createFromBinary($data->user_id),
            $data->created_at,
            $data->expiry
        );
    }
}
