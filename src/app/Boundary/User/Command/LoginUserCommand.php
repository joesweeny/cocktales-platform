<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;

class LoginUserCommand implements Command
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
