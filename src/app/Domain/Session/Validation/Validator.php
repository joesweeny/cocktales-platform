<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class Validator
{
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Clock $clock, LoggerInterface $logger)
    {
        $this->clock = $clock;
        $this->logger = $logger;
    }

    /**
     * @param SessionToken $token
     * @param Uuid $userId
     * @return bool
     */
    public function validate(SessionToken $token, Uuid $userId): bool
    {
        return !$this->hasTokenExpired($token) && $this->tokenBelongsToUser($token, $userId);
    }

    /**
     * @param SessionToken $token
     * @return bool
     */
    private function hasTokenExpired(SessionToken $token): bool
    {
        return $token->getExpiry() < $this->clock->now();
    }

    private function tokenBelongsToUser(SessionToken $token, Uuid $userId): bool
    {
        if ($token->getUserId()->__toString() !== $userId->__toString()) {
            $this->logger->error("User {$userId} has attempted to use Token {$token->getToken()} that does not belong to them");
            return false;
        }

        return true;

    }
}
