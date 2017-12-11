<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;

class TokenRefresher
{
    const HOUR_IN_SECONDS = 60 * 60;

    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * @param SessionToken $token
     * @return SessionToken
     */
    public function refreshToHour(SessionToken $token): SessionToken
    {
        $token->setExpiry($this->clock->now()->add(new \DateInterval('PT1H')));

        return $token;
    }

    /**
     * @param \DateTimeImmutable $expires
     * @return bool
     */
    public function expiresWithinHour(\DateTimeImmutable $expires): bool
    {
        $diffInSeconds = $expires->getTimestamp() - $this->clock->now()->getTimestamp();

        return $diffInSeconds > 0 && $diffInSeconds < self::HOUR_IN_SECONDS;
    }
}
