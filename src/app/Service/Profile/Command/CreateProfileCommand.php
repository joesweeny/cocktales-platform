<?php

namespace Cocktales\Service\Profile\Command;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\CommandBus\Command;

class CreateProfileCommand implements Command
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $username;

    /**
     * CreateProfileCommand constructor.
     * @param User $user
     * @param string $username
     */
    public function __construct(User $user, string $username)
    {
        $this->user = $user;
        $this->username = $username;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
