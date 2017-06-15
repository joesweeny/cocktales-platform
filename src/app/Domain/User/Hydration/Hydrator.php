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
        return (new User)
            ->setId(Uuid::createFromBinary($data->id))
            ->setEmail($data->email)
            ->setPasswordHash(new PasswordHash($data->password))
            ->setCreatedDate(new \DateTimeImmutable($data->created_at))
            ->setLastModifiedDate(new \DateTimeImmutable($data->updated_at));
    }

    public static function toPublicViewableData(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->__toString(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedDate()->format('d-m-Y'),
            'updated_at' => $user->getLastModifiedDate()->format('d-m-Y')
        ];
    }
}
