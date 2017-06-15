<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\Profile\Command\GetProfileCommand;
use PHPUnit\Framework\TestCase;

class GetProfileCommandHandlerTest extends TestCase
{
    public function test_handle_returns_a_profile_with_associated_user_id()
    {
        /** @var ProfileOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(ProfileOrchestrator::class);
        /** @var GetProfileCommandHandler $handler */
        $handler = new GetProfileCommandHandler($orchestrator->reveal());

        $command = new GetProfileCommand('6d1a214e-8659-4492-a36a-ea7f72e0ee44');

        $orchestrator->getProfileByUserId(new Uuid('6d1a214e-8659-4492-a36a-ea7f72e0ee44'))->shouldBeCalled()->willReturn(
            (new Profile)->setUserId(new Uuid('6d1a214e-8659-4492-a36a-ea7f72e0ee44'))
        );

        $profile = $handler->handle($command);

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('6d1a214e-8659-4492-a36a-ea7f72e0ee44', $profile->getUserId()->__toString());
    }
}
