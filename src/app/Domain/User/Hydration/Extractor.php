<?php

namespace Cocktales\Domain\User\Hydration;

use Cocktales\Domain\User\Entity\User;

final class Extractor
{
    /**
     * @param User $user
     * @return User|\stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public static function toRawData(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->toBinary(),
            'email' => $user->getEmail(),
            'password' => $user->getPasswordHash(),
            'created_at' => $user->getCreatedDate(),
            'updated_at' => $user->getLastModifiedDate()
        ];
    }
}
