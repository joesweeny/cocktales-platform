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

    /**
     * @param Profile $profile
     * @return \stdClass
     */
    public static function toPublicViewableData(Profile $profile): \stdClass
    {
        return (object) [
            'user_id' => $profile->getUserId()->__toString(),
            'username' => $profile->getUsername(),
            'first_name' => $profile->getFirstName(),
            'last_name' => $profile->getLastName(),
            'location' => $profile->getLocation(),
            'slogan' => $profile->getSlogan(),
            'created_at' => $profile->getCreatedDate()->format('d/m/Y'),
            'updated_at' => $profile->getLastModifiedDate()->format('d/m/Y')
        ];
    }
}
