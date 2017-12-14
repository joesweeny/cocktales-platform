<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class LogoutUserCommand implements Command
{
    /**
     * @var Uuid
     */
    private $token;
    /**
     * @var Uuid
     */
    private $userId;

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
