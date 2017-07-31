<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Persistence\Repository;

class AvatarOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * AvatarOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Avatar $avatar
     * @return Avatar
     * @throws \Cocktales\Domain\Avatar\Exception\AvatarRepositoryException
     */
    public function createAvatar(Avatar $avatar): Avatar
    {
        return $this->repository->createAvatar($avatar);
    }
}
