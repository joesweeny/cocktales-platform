<?php

namespace Cocktales\Domain\User;

use Cocktales\Domain\User\Entity\User;

class UserPresenter
{
    /**
     * @param User $user
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function toDto(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->__toString(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedDate()->format('d-m-Y'),
            'updated_at' => $user->getLastModifiedDate()->format('d-m-Y')
        ];
    }
}
