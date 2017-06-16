<?php

namespace Cocktales\Domain\User\Validation;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  Validator */
    private $validator;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->validator = new Validator($this->orchestrator->reveal());
    }

    public function test_is_email_unique_returns_true_if_email_is_not_in_the_database()
    {
        $this->orchestrator->getUserByEmail('joe@email.com')->shouldBeCalled()->willThrow(NotFoundException::class);

        $this->assertTrue($this->validator->isEmailUnique('joe@email.com'));
    }

    public function test_is_unique_returns_false_if_email_is_already_in_the_database()
    {
        $this->orchestrator->getUserByEmail('joe@email.com')->shouldBeCalled()->willReturn((new User)->setEmail('joe@email.com'));

        $this->assertFalse($this->validator->isEmailUnique('joe@email.com'));
    }
}
