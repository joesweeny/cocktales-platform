<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;

class Validator
{
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * @param SessionToken $token
     * @return bool
     */
    public function isValid(SessionToken $token): bool
    {
        return !$this->hasTokenExpired($token);
    }

    /**
     * @param SessionToken $token
     * @return bool
     */
    private function hasTokenExpired(SessionToken $token): bool
    {
        return $token->getExpiry() < $this->clock->now();
    }
}
