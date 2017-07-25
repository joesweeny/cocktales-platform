<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Password\PasswordHash;

class RegisterUserCommand implements Command
{
    /**
     * @var \stdClass
     */
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
    public function getPassword(): PasswordHash
    {
        return PasswordHash::createFromRaw($this->data->password);
    }
}
