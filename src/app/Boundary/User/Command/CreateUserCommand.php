<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Password\PasswordHash;

class CreateUserCommand implements Command
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;


    /**
     * CreateUserCommand constructor.
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return PasswordHash
     */
    public function getPassword(): PasswordHash
    {
        return PasswordHash::createFromRaw($this->password);
    }
}
