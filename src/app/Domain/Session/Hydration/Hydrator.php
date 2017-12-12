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
            (new \DateTimeImmutable)->setTimestamp($data->created_at),
            (new \DateTimeImmutable)->setTimestamp($data->expiry)
        );
    }
}
