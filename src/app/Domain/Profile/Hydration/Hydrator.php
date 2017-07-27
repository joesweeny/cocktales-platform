<?php

namespace Cocktales\Domain\Profile\Hydration;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    /**
     * @param \stdClass $data
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public static function fromRawData(\stdClass $data): Profile
    {
        return (new Profile)
            ->setUserId(Uuid::createFromBinary($data->user_id))
            ->setUsername($data->username)
            ->setFirstName($data->first_name)
            ->setLastName($data->last_name)
            ->setLocation($data->location)
            ->setSlogan($data->slogan)
            ->setCreatedDate(new \DateTimeImmutable($data->created_at))
            ->setLastModifiedDate(new \DateTimeImmutable($data->updated_at));
    }
}
