<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;

class GetUserByEmailCommand implements Command
{
    /**
     * @var string
     */
    private $email;

    /**
     * GetUserByEmailCommand constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
