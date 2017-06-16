<?php

namespace Cocktales\Domain\User;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\Persistence\Repository;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

class UserOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * UserOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User
    {
        return $this->repository->createUser($user);
    }

    /**
     * @param string $email
     * @return User
     * @throws NotFoundException
     */
    public function getUserByEmail(string $email): User
    {
        return $this->repository->getUserByEmail($email);
    }

    /**
     * @param Uuid $id
     * @return User
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getUserById(Uuid $id): User
    {
        return $this->repository->getUserById($id);
    }

    /**
     * @param User $user
     * @return User
     */
    public function updateUser(User $user): User
    {
        return $this->repository->updateUser($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user)
    {
       $this->repository->deleteUser($user);
    }

    public function canCreateNewUser(User $user): bool
    {
        try {
            $this->getUserByEmail($user->getEmail());
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }
}
