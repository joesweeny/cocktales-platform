<?php

namespace Cocktales\Boundary\Session\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class CreateSessionTokenCommand implements Command
{
    /**
     * @var string
     */
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): Uuid
    {
        return new Uuid($this->userId);
    }
}
