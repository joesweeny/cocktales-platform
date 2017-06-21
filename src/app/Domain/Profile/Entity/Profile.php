<?php

namespace Cocktales\Domain\Profile\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;

class Profile
{
    use PrivateAttributesTrait;
    use TimestampedTrait;

    /**
     * @param Uuid $userId
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setUserId(Uuid $userId): Profile
    {
        return $this->set('user_id', $userId);
    }

    /**
     * @return Uuid
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function getUserId(): Uuid
    {
        return $this->getOrFail('user_id');
    }

    /**
     * @param string $name
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setFirstName(string $name): Profile
    {
        return $this->set('first_name', $name);
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->get('first_name', '');
    }

    /**
     * @param string $name
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setLastName(string $name): Profile
    {
        return $this->set('last_name', $name);
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->get('last_name', '');
    }

    /**
     * @param string $location
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setLocation(string $location): Profile
    {
        return $this->set('location', $location);
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->get('location', '');
    }

    /**
     * @param string $slogan
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setSlogan(string $slogan): Profile
    {
        return $this->set('slogan', $slogan);
    }

    /**
     * @return string
     */
    public function getSlogan(): string
    {
        return $this->get('slogan', '');
    }

    /**
     * @param string $username
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function setUsername(string $username): Profile
    {
        return $this->set('username', $username);
    }

    /**
     * @return string
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function getUsername(): string
    {
        return $this->getOrFail('username');
    }
}
