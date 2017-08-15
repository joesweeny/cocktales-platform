<?php

namespace Cocktales\Domain\Avatar\Persistence;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Add a new Avatar record to the database
     *
     * @param Avatar $avatar
     * @throws AvatarRepositoryException
     * @return Avatar
     */
    public function createAvatar(Avatar $avatar): Avatar;

    /**
     * @param Uuid $userId
     * @throws NotFoundException
     * @return Avatar
     */
    public function getAvatarByUserId(Uuid $userId): Avatar;
}
