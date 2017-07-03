<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Service\Profile\Command\CreateProfileCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateProfileCommandHandlerTest extends TestCase
{
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  CreateProfileCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(ProfileOrchestrator::class);
        $this->handler = new CreateProfileCommandHandler($this->orchestrator->reveal());
    }

    public function test_handle_creates_a_new_profile_record_in_the_database()
    {
        $command = new CreateProfileCommand((object) [
            'user_id' => '8897fa60-e66f-41fb-86a2-9828b1785481',
            'username' => 'joe',
            'first_name' => 'Joe',
            'last_name' => 'Sweeny',
            'location' => '',
            'slogan' => ''
        ]);

        $this->orchestrator->isUsernameUnique($command->getUsername())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->createProfile(Argument::that(function (Profile $profile) {
            $this->assertEquals('8897fa60-e66f-41fb-86a2-9828b1785481', $profile->getUserId()->__toString());
            $this->assertEquals('joe', $profile->getUsername());
            $this->assertEquals('Joe', $profile->getFirstName());
            $this->assertEquals('Sweeny', $profile->getLastName());
            $this->assertEquals('', $profile->getLocation());
            $this->assertEquals('', $profile->getSlogan());
            return true;
        }))->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_throws_username_validation_exception_if_username_is_taken_by_another_user()
    {
        $command = new CreateProfileCommand((object) [
            'user_id' => '8897fa60-e66f-41fb-86a2-9828b1785481',
            'username' => 'joe',
            'first_name' => 'Joe',
            'last_name' => 'Sweeny',
            'location' => '',
            'slogan' => ''
        ]);

        $this->orchestrator->isUsernameUnique($command->getUsername())->shouldBeCalled()->willReturn(false);

        $this->orchestrator->createProfile(Argument::type(Profile::class))->shouldNotBeCalled();

        $this->expectException(UsernameValidationException::class);
        $this->handler->handle($command);
    }
}
