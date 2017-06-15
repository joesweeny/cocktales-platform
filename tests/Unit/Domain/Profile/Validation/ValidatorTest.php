<?php

namespace Cocktales\Domain\Profile\Validation;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use DI\Container;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    use RunsMigrations;
    use CreatesContainer;

    /** @var  Container */
    private $container;
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  Validator */
    private $validator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(ProfileOrchestrator::class);
        $this->validator = new Validator($this->orchestrator);
    }

    public function test_is_username_unique_returns_true_if_username_is_not_already_taken()
    {
        $this->createProfile();
        $this->assertTrue($this->validator->isUsernameUnique('joe'));
    }

    public function test_is_username_unique_returns_false_if_username_is_already_taken()
    {
        $this->createProfile();
        $this->assertFalse($this->validator->isUsernameUnique('andrea'));
    }

    private function createProfile()
    {
        $this->orchestrator->createProfile((new Profile)
            ->setId(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'))
            ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
            ->setUsername('andrea')
            ->setFirstName('Joe')
            ->setLastName('Sweeny')
            ->setCity('Romford')
            ->setCounty('Essex')
            ->setSlogan('Be drunk and Merry')
            ->setAvatar('newpic.jpg'));
    }
}
