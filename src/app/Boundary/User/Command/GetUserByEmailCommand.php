<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;

class GetUserByEmailCommand implements Command
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
