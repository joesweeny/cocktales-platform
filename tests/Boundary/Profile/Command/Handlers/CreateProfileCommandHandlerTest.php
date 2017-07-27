<?php

namespace Cocktales\Boundary\Profile\Command\Handlers;

use Cake\Chronos\Chronos;
use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Domain\Profile\ProfilePresenter;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Boundary\Profile\Command\CreateProfileCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateProfileCommandHandlerTest extends TestCase
{
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  CreateProfileCommandHandler */
    private $handler;
    /** @var  ProfilePresenter */
    private $presenter;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(ProfileOrchestrator::class);
        $this->presenter = $this->prophesize(ProfilePresenter::class);
        $this->handler = new CreateProfileCommandHandler($this->orchestrator->reveal(), $this->presenter->reveal());
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

        $this->presenter->toDto(Argument::type(Profile::class))->willReturn(
            (object) [
                'user_id' => '8897fa60-e66f-41fb-86a2-9828b1785481',
                'username' => 'joe',
                'first_name' => 'Joe',
                'last_name' => 'Sweeny',
                'location' => '',
                'slogan' => '',
                'created_at' => Chronos::now()->format('d/m/Y'),
                'updated_at' => Chronos::now()->format('d/m/Y')
            ]
        );

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

        $this->presenter->toDto(Argument::type(Profile::class))->shouldNotBeCalled();

        $this->expectException(UsernameValidationException::class);
        $this->handler->handle($command);
    }
}
