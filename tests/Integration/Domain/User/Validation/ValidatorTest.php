<?php

namespace Cocktales\Domain\User\Validation;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    use RunsMigrations;
    use CreatesContainer;

    /** @var  Container */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  Validator */
    private $validator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->validator = new Validator($this->orchestrator);
    }

    public function test_is_email_unique_returns_true_if_email_is_not_already_taken()
    {
        $this->createUser();
        $this->assertTrue($this->validator->isEmailUnique('andrea@unique.com'));
    }

    public function test_is_email_unique_returns_false_if_email_is_already_taken()
    {
        $this->createUser();
        $this->assertFalse($this->validator->isEmailUnique('joe@example.com'));
    }

    public function test_has_username_been_updated_returns_true_if_the_username_passed_in_is_different_to_that_stored_in_the_database()
    {
        $this->createUser();

        $this->assertTrue($this->validator->hasUserNameBeenUpdated('andrea', new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d')));
    }

    public function test_has_username_been_updated_returns_false_if_userame_passed_in_is_the_same_to_that_stored_in_the_database()
    {
        $this->createUser();

        $this->assertFalse($this->validator->hasUserNameBeenUpdated('joe', new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d')));
    }

    private function createUser()
    {
        $this->orchestrator->createUser(
            (new User)
                ->setId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
    }
}
