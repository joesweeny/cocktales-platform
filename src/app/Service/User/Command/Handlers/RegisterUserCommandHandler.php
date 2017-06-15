<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\Validation\Validator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\User\Command\RegisterUserCommand;

class RegisterUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var Validator
     */
    private $validator;

    /**
     * RegisterUserCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param Validator $validator
     */
    public function __construct(UserOrchestrator $orchestrator, Validator $validator)
    {
        $this->orchestrator = $orchestrator;
        $this->validator = $validator;
    }

    public function handle(RegisterUserCommand $command)
    {
        if (!$this->validator->isEmailUnique($command->getEmail())) {
            throw new \Exception();
        }

        return $this->orchestrator->createUser($this->createUserEntity($command));
    }

    /**
     * @param RegisterUserCommand $command
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    private function createUserEntity(RegisterUserCommand $command): User
    {
        return (new User)
            ->setId(Uuid::generate())
            ->setEmail($command->getEmail())
            ->setPasswordHash(PasswordHash::createFromRaw($command->getPasswordHash()));
    }
}
