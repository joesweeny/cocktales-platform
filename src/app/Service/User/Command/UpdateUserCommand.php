<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;

class UpdateUserCommand implements Command
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

    public function getId(): string
    {
        return $this->data->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->data->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->data->email;
    }
}
