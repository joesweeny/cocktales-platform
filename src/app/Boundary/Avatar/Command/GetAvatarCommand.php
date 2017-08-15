<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetAvatarCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;

    /**
     * GetAvatarCommand constructor.
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = new Uuid($userId);
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
