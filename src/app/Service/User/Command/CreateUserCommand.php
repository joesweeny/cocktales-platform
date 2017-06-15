<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Password\PasswordHash;

class CreateUserCommand implements Command
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * CreateUserCommand constructor.
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
