<?php

namespace Cocktales\Domain\Profile;

use Cocktales\Domain\Profile\Entity\Profile;

class ProfilePresenter
{
    /**
     * @param Profile $profile
     * @return \stdClass
     */
    public function toDto(Profile $profile): \stdClass
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
