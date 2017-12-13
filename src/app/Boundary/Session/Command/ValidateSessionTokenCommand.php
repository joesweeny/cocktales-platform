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
     * @var Uuid
     */
    private $userId;

    /**
     * ValidateSessionTokenCommand constructor.
     * @param string $token
     * @param string $userId
     */
    public function __construct(string $token, string $userId)
    {
        $this->token = new Uuid($token);
        $this->userId = new Uuid($userId);
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
