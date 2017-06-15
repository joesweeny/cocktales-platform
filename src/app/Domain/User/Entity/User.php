<?php

namespace Cocktales\Domain\User\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;

class User
{
    use PrivateAttributesTrait;
    use TimestampedTrait;

    /**
     * @param Uuid $id
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setId(Uuid $id): User
    {
        return $this->set('id', $id);
    }

    /**
     * @return Uuid
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function getId(): Uuid
    {
        return $this->getOrFail('id');
    }

    /**
     * @param string $email
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setEmail(string $email): User
    {
        return $this->set('email', $email);
    }

    /**
     * @return string
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function getEmail(): string
    {
        return $this->getOrFail('email');
    }

    /**
     * @param PasswordHash $password
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setPasswordHash(PasswordHash $password): User
    {
        return $this->set('password', $password);
    }

    /**
     * @return PasswordHash
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function getPasswordHash(): PasswordHash
    {
        return $this->getOrFail('password');
    }
}
