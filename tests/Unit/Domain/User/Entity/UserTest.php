<?php

namespace Cocktales\Domain\User\Entity;

use Cocktales\Framework\Exception\ActionNotSupportedException;
use Cocktales\Framework\Exception\UndefinedException;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_getters_and_setter_methods_on_user_entity()
    {
        $user = (new User)
            ->setId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setEmail('joe@example.com')
            ->setPasswordHash(PasswordHash::createFromRaw('password'))
            ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'));

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $user->getId()->__toString());
        $this->assertEquals('joe@example.com', $user->getEmail());
        $this->assertTrue($user->getPasswordHash()->verify('password'));
        $this->assertEquals('2017-05-03 21:39:00', $user->getCreatedDate());
        $this->assertEquals('2017-05-03 21:39:00', $user->getLastModifiedDate());
    }

    public function test_action_not_supported_exception_thrown_if_setting_a_property_that_is_already_set()
    {
        $user = (new User)->setEmail('joe@example.com');
        $this->expectException(ActionNotSupportedException::class);
        $this->expectExceptionMessage('Attempted to modify the value of property `email` but this property is immutable');
        $user->setEmail('new@email.com');
    }
}
