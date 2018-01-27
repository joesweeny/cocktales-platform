<?php

namespace Cocktales\Boundary\Session\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class ValidateSessionTokenCommand implements Command
{
    /**
     * @var Uuid
     */
    private $token;

    /**
     * ValidateSessionTokenCommand constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = new Uuid($token);
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }
}
