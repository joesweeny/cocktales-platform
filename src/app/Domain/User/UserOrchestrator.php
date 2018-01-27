<?php

namespace Cocktales\Domain\User;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Persistence\Repository as TokenRepository;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\Persistence\Repository;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class UserOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var TokenRepository
     */
    private $tokenRepo;

    public function __construct(Repository $repository, TokenRepository $tokenRepo)
    {
        $this->repository = $repository;
        $this->tokenRepo = $tokenRepo;
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

    /**
     * @param User $user
     * @return bool
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function canCreateNewUser(User $user): bool
    {
        try {
            $this->getUserByEmail($user->getEmail());
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function canUpdateUser(string $email): bool
    {
        try {
            $this->getUserByEmail($email);
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    /**
     * @param Uuid $id
     * @param string $password
     * @return bool
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function validateUserPassword(Uuid $id, string $password): bool
    {
        $user = $this->getUserById($id);

        return $user->getPasswordHash()->verify($password);
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->repository->getUsers();
    }

    /**
     * @param SessionToken $token
     * @return User
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getUserByToken(SessionToken $token): User
    {
        return $this->repository->getUserById($token->getUserId());
    }
}
