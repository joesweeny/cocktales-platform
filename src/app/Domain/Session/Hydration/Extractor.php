<?php

namespace Cocktales\Domain\Session\Hydration;

use Cocktales\Domain\Session\Entity\SessionToken;

class Extractor
{
    public static function toRawData(SessionToken $token): \stdClass
    {
        return (object) [
            'token' => $token->getToken()->toBinary(),
            'user_id' => $token->getUserId()->toBinary(),
            'created_at' => $token->getCreatedAt()->getTimestamp(),
            'expiry' => $token->getExpiry()->getTimestamp()
        ];
    }
}
