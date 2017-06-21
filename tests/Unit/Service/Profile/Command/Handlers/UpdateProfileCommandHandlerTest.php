<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\Profile\Command\UpdateProfileCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class UpdateProfileCommandHandlerTest extends TestCase
{
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  UpdateProfileCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(ProfileOrchestrator::class);
        $this->handler = new UpdateProfileCommandHandler($this->orchestrator->reveal());
    }

    public function test_handle_updates_profile_record_in_the_database()
    {
        $command = new UpdateProfileCommand((object) [
            'user_id' => '8897fa60-e66f-41fb-86a2-9828b1785481',
            'username' => 'joe',
            'first_name' => 'Joe',
            'last_name' => 'Sweeny',
            'location' => '',
            'slogan' => 'I want to get pissed'
        ]);

        $this->orchestrator->getProfileByUserId($command->getUserId())->shouldBeCalled()->willReturn(
            (new Profile)
                ->setUserId(new Uuid('8897fa60-e66f-41fb-86a2-9828b1785481'))
                ->setUsername('Andrea')
                ->setFirstName('Andrea')
                ->setFirstName('Sweeny')
                ->setLocation('')
                ->setSlogan('Love Life')
        );

        $this->orchestrator->isUsernameUnique($command->getUsername())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->updateProfile(Argument::that(function (Profile $profile) {
            $this->assertEquals('8897fa60-e66f-41fb-86a2-9828b1785481', $profile->getUserId()->__toString());
            $this->assertEquals('joe', $profile->getUsername());
            $this->assertEquals('Joe', $profile->getFirstName());
            $this->assertEquals('Sweeny', $profile->getLastName());
            $this->assertEquals('', $profile->getLocation());
            $this->assertEquals('I want to get pissed', $profile->getSlogan());
            return true;
        }))->shouldBeCalled();

        $this->handler->handle($command);
    }
}
