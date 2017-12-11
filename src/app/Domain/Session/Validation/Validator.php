<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class Validator
{
    /**
     * @var TokenOrchestrator
     */
    private $orchestrator;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(TokenOrchestrator $orchestrator, Clock $clock, LoggerInterface $logger)
    {
        $this->orchestrator = $orchestrator;
        $this->clock = $clock;
        $this->logger = $logger;
    }

    /**
     * @param Uuid $token
     * @param Uuid $userId
     * @return bool
     */
    public function validate(Uuid $token, Uuid $userId): bool
    {
        return !$this->hasTokenExpired($token) && $this->tokenBelongsToUser($token, $userId);
    }

    /**
     * @param Uuid $token
     * @return bool
     */
    private function hasTokenExpired(Uuid $token): bool
    {
        try {
            $token = $this->orchestrator->getToken($token);

            return $token->getExpiry() < $this->clock->now();
        } catch (NotFoundException $e) {
            return false;
        }
    }

    private function tokenBelongsToUser(Uuid $token, Uuid $userId): bool
    {
        try {
            $token = $this->orchestrator->getToken($token);

            if ($token->getUserId()->__toString() !== $userId->__toString()) {
                $this->logger->error("User {$userId} has attempted to use Token {$token->getToken()} that does not belong to them");
                return false;
            }

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
