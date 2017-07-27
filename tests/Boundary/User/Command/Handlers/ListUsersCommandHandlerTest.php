<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\ListUsersCommand;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Password\PasswordHash;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ListUsersCommandHandlerTest extends TestCase
{
    public function test_handle_returns_an_array_of_objects_containing_user_information()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var UserPresenter $presenter */
        $presenter = $this->prophesize(UserPresenter::class);
        $handler = new ListUsersCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $orchestrator->getUsers()->willReturn(new Collection([
            $user1 = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00')),
            $user2 = (new User('77e2438d-a744-4590-9785-08917dcdeb75'))
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        ]));

        $presenter->toDto($user1)->shouldBeCalled();
        $presenter->toDto($user2)->shouldBeCalled();

        $handler->handle(new ListUsersCommand);
    }
}
