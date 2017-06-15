<?php

namespace Cocktales\Domain\Profile;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\Persistence\Repository;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

class ProfileOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * ProfileOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Profile $profile
     * @return Profile
     */
    public function createProfile(Profile $profile): Profile
    {
        return $this->repository->createProfile($profile);
    }

    /**
     * @param Uuid $id
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getProfileByUserId(Uuid $id): Profile
    {
        return $this->repository->getProfileByUserId($id);
    }

    /**
     * @param string $username
     * @return Profile
     * @throws NotFoundException
     */
    public function getProfileByUsername(string $username): Profile
    {
        return $this->repository->getProfileByUsername($username);
    }

    /**
     * @param Profile $profile
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function updateProfile(Profile $profile): Profile
    {
        return $this->repository->updateProfile($profile);
    }

    /**
     * @param User $user
     * @return Profile
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function createProfileEntityFromUser(User $user, string $username): Profile
    {
        return (new Profile)
            ->setId(Uuid::generate())
            ->setUserId($user->getId())
            ->setUsername($username);
    }
}
