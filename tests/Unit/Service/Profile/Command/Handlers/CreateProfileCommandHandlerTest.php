<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\Profile\Command\CreateProfileCommand;
use PHPUnit\Framework\TestCase;

class CreateProfileCommandHandlerTest extends TestCase
{
    public function test_handle_creates_a_new_profile()
    {
        /** @var ProfileOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(ProfileOrchestrator::class);
        $handler = new CreateProfileCommandHandler($orchestrator->reveal());

        $command = new CreateProfileCommand($user = (new User)->setId(new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7')), 'joe');

        $orchestrator->createProfileEntityFromUser($command->getUser(), $command->getUsername())->shouldBeCalled()->willReturn(
            $profile = (new Profile)->setUserId($user->getId())
        );

        $orchestrator->createProfile($profile)->shouldBeCalled();

        $handler->handle($command);
    }
}
