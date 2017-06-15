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
    public function test_handle_updates_a_profile_record_in_the_database()
    {
        /** @var ProfileOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(ProfileOrchestrator::class);
        $handler = new UpdateProfileCommandHandler($orchestrator->reveal());

        $command = new UpdateProfileCommand((object) [
            'user_id' => 'b5acd30c-085e-4dee-b8a9-19e725dc62c3',
            'first_name' => 'Joe',
            'last_name' => 'Sweeny',
            'city' => 'Consett',
            'county' => 'Durham',
            'slogan' => 'Never eat yellow snow'
        ]);

        $orchestrator->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))->shouldBeCalled()->willReturn(
            (new Profile)
                ->setId(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'))
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setCity('Romford')
                ->setCounty('Essex')
                ->setSlogan('Be drunk and Merry')
        );

        $orchestrator->updateProfile(Argument::that(function (Profile $profile) {
            $this->assertEquals('Joe', $profile->getFirstName());
            $this->assertEquals('Sweeny', $profile->getLastName());
            $this->assertEquals('Consett', $profile->getCity());
            $this->assertEquals('Durham', $profile->getCounty());
            $this->assertEquals('Never eat yellow snow', $profile->getSlogan());
            return true;
        }))->shouldBeCalled();

        $handler->handle($command);
    }
}
