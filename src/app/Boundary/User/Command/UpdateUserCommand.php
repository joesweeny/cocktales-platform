<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;

class UpdateUserCommand implements Command
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
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return new Uuid($this->data->id);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->data->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->data->password;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->data->newPassword;
    }
}
