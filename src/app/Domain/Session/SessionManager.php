<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\Validation\Validator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        TokenOrchestrator $orchestrator,
        Validator $validator,
        TokenRefresher $refresher
    ) {
        $this->orchestrator = $orchestrator;
        $this->validator = $validator;
        $this->refresher = $refresher;
    }

    /**
     * @param Uuid $token
     * @throws SessionTokenValidationException
     * @return void
     */
    public function handleToken(Uuid $token): void
    {
        try {
            $token = $this->orchestrator->getToken($token);

            if (!$this->validator->isValid($token)) {
                throw new SessionTokenValidationException('Unable to validate token provided');
            }

            $this->handleTokenExpiry($token);
            return;
        } catch (NotFoundException $e) {
            throw new SessionTokenValidationException($e->getMessage());
        }
    }

    private function handleTokenExpiry(SessionToken $token): void
    {
        if ($this->refresher->expiresWithinHour($token->getExpiry())) {
            $this->orchestrator->updateToken($this->refresher->refreshToHour($token));
        }
    }
}
