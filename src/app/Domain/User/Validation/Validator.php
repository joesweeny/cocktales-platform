<?php

namespace Cocktales\Domain\User\Validation;

use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;

class Validator
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * Validation constructor.
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isEmailUnique(string $email): bool
    {
        try {
            $this->orchestrator->getUserByEmail($email);
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }
}
