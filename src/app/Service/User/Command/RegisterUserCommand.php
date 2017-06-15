<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Password\PasswordHash;

class RegisterUserCommand implements Command
{
    private $data;

    /**
     * RegisterUserCommand constructor.
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->data->email;
    }

    /**
     * @return PasswordHash
     */
    public function getPasswordHash(): PasswordHash
    {
        return new PasswordHash($this->data->password);
    }
}
