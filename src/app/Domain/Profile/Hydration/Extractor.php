<?php

namespace Cocktales\Domain\Profile\Hydration;

use Cocktales\Domain\Profile\Entity\Profile;

class Extractor
{
    /**
     * @param Profile $profile
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public static function toRawData(Profile $profile): \stdClass
    {
        return (object) [
            'id' => $profile->getId()->toBinary(),
            'user_id' => $profile->getUserId()->toBinary(),
            'username' => $profile->getUsername(),
            'first_name' => $profile->getFirstName(),
            'last_name' => $profile->getLastName(),
            'location' => $profile->getLocation(),
            'slogan' => $profile->getSlogan(),
            'avatar' => $profile->getAvatar(),
            'created_at' => $profile->getCreatedDate(),
            'updated_at' => $profile->getLastModifiedDate()
        ];
    }
}
