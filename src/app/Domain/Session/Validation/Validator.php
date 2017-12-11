<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

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

    public function __construct(TokenOrchestrator $orchestrator, Clock $clock)
    {
        $this->orchestrator = $orchestrator;
        $this->clock = $clock;
    }

    /**
     * @param Uuid $token
     * @return bool
     */
    public function hasTokenExpired(Uuid $token): bool
    {
        try {
            $token = $this->orchestrator->getToken($token);

            return $token->getExpiry() < $this->clock->now();
        } catch (NotFoundException $e) {
            return false;
        }
    }

    public function tokenBelongsToUser(Uuid $token, Uuid $userId): bool
    {
        try {
            $token = $this->orchestrator->getToken($token);

            return (string) $token->getUserId() === (string) $userId;
        } catch (NotFoundException $e) {
            return false;
        }
    }

}
