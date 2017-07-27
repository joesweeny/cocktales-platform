<?php

namespace Cocktales\Boundary\Profile\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetProfileByUserIdCommand implements Command
{
    /**
     * @var string
     */
    private $userId;

    /**
     * GetProfileByUserIdCommand constructor.
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return new Uuid($this->userId);
    }
}
