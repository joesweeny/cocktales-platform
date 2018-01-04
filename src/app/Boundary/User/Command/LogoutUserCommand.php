<?php

namespace Cocktales\Boundary\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class LogoutUserCommand implements Command
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $userId;

    public function __construct(string $token, string $userId)
    {
        $this->token = $token;
        $this->userId = $userId;
    }

    public function getToken(): Uuid
    {
        return new Uuid($this->token);
    }

    public function getUserId(): Uuid
    {
        return new Uuid($this->userId);
    }
}
