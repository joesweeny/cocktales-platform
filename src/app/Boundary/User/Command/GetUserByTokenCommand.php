<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetUserByTokenCommand implements Command
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): Uuid
    {
        return new Uuid($this->token);
    }
}
