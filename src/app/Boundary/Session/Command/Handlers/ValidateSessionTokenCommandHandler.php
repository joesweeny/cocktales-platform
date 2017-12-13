<?php

namespace Cocktales\Boundary\Session\Command\Handlers;

use Cocktales\Boundary\Session\Command\ValidateSessionTokenCommand;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\SessionManager;

class ValidateSessionTokenCommandHandler
{
    /**
     * @var SessionManager
     */
    private $manager;

    public function __construct(SessionManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(ValidateSessionTokenCommand $command)
    {
        try {
            $this->manager->handleToken($command->getToken(), $command->getUserId());
            return true;
        } catch (SessionTokenValidationException $e) {
            return false;
        }
    }
}
