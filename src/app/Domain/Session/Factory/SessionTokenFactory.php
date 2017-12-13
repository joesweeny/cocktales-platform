<?php

namespace Cocktales\Domain\Session\Factory;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Uuid\Uuid;

class SessionTokenFactory
{
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function createLongToken(Uuid $userId): SessionToken
    {
        return new SessionToken(
            Uuid::generate(),
            $userId,
            $this->clock->now(),
            $this->clock->now()->addHours(4)
        );
    }
}
