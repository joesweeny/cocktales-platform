<?php

namespace Cocktales\Domain\User\Hydration;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;

final class Hydrator
{
    /**
     * @param \stdClass $data
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public static function fromRawData(\stdClass $data): User
    {
        return (new User(Uuid::createFromBinary($data->id)))
            ->setEmail($data->email)
            ->setPasswordHash(new PasswordHash($data->password))
            ->setCreatedDate(new \DateTimeImmutable($data->created_at))
            ->setLastModifiedDate(new \DateTimeImmutable($data->updated_at));
    }
}
