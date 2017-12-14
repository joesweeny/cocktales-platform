<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\Validation\Validator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

class SessionManager
{
    /**
     * @var TokenOrchestrator
     */
    private $orchestrator;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var TokenRefresher
     */
    private $refresher;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(
        TokenOrchestrator $orchestrator,
        Validator $validator,
        TokenRefresher $refresher,
        Clock $clock
    ) {
        $this->orchestrator = $orchestrator;
        $this->validator = $validator;
        $this->refresher = $refresher;
        $this->clock = $clock;
    }

    /**
     * @param Uuid $token
     * @param Uuid $userId
     * @throws SessionTokenValidationException
     * @return void
     */
    public function handleToken(Uuid $token, Uuid $userId): void
    {
        try {
            $token = $this->orchestrator->getToken($token);
            $this->validateToken($token, $userId);
            $this->handleTokenExpiry($token);
            return;
        } catch (NotFoundException $e) {
            throw new SessionTokenValidationException($e->getMessage());
        }
    }

    /**
     * @param Uuid $token
     * @return void
     * @throws \Cocktales\Domain\Session\Exception\SessionTokenValidationException
     */
    public function expireToken(Uuid $token): void
    {
        try {
            $token = $this->orchestrator->getToken($token);

            $this->orchestrator->updateToken($token->setExpiry($this->clock->now()));
        }catch (NotFoundException $e) {
            throw new SessionTokenValidationException($e->getMessage());
        }

    }

    /**
     * @param SessionToken $token
     * @param Uuid $userId
     * @throws SessionTokenValidationException
     * @return void
     */
    private function validateToken(SessionToken $token, Uuid $userId): void
    {
        if (!$this->validator->validate($token, $userId)) {
            throw new SessionTokenValidationException('Unable to validation token provided');
        }
    }

    private function handleTokenExpiry(SessionToken $token): void
    {
        if ($this->refresher->expiresWithinHour($token->getExpiry())) {
            $this->orchestrator->updateToken($this->refresher->refreshToHour($token));
        }
    }
}
