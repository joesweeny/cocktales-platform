<?php

namespace Cocktales\Domain\User\Persistence;


use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Create a new by adding a new User entry into the database
     *
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User;

    /**
     * Retrieves a user by their email
     *
     * @param string $email
     * @return User
     * @throws NotFoundException
     */
    public function getUserByEmail(string $email): User;

    /**
     * @param Uuid $id
     * @return User
     * @throws NotFoundException
     */
    public function getUserById(Uuid $id): User;

    /**
     * @param User $user
     * @return User
     */
    public function updateUser(User $user): User;

    /**
     * Deletes a user from the database
     *
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user);
}
