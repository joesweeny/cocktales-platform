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
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        TokenOrchestrator $orchestrator,
        Validator $validator,
        TokenRefresher $refresher,
        LoggerInterface $logger
    ) {
        $this->orchestrator = $orchestrator;
        $this->validator = $validator;
        $this->refresher = $refresher;
        $this->logger = $logger;
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
            $this->logError($token, $userId);
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
            throw new SessionTokenValidationException('Unable to validate token provided');
        }
    }

    private function handleTokenExpiry(SessionToken $token): void
    {
        if ($this->refresher->expiresWithinHour($token->getExpiry())) {
            $this->orchestrator->updateToken($this->refresher->refreshToHour($token));
        }
    }

    private function logError(Uuid $token, Uuid $userId): void
    {
        $this->logger->error(
            'A user has attempted to again access with an invalid id and token combination',
            [
                'token' => (string) $token,
                'userId' => (string) $userId
            ]
        );
    }
}
