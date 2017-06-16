<?php

namespace Cocktales\Domain\User\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Identity\IdentifiedByUuidTrait;
use Cocktales\Framework\Password\PasswordHash;

class User
{
    use IdentifiedByUuidTrait;
    use PrivateAttributesTrait;
    use TimestampedTrait;

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
