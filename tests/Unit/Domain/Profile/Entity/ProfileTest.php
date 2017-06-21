<?php

namespace Cocktales\Domain\Profile\Entity;

use Cocktales\Framework\Exception\UndefinedException;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function test_setters_and_getters_on_profile_entity()
    {
        $profile = (new Profile)
            ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
            ->setUsername('joe')
            ->setFirstName('Joe')
            ->setLastName('Sweeny')
            ->setLocation('Essex')
            ->setSlogan('Be drunk and Merry')
            ->setCreatedDate(new \DateTimeImmutable('2017-06-06 13:43:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-06-06 13:43:00'));

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('b5acd30c-085e-4dee-b8a9-19e725dc62c3', $profile->getUserId()->__toString());
        $this->assertEquals('joe', $profile->getUsername());
        $this->assertEquals('Joe', $profile->getFirstName());
        $this->assertEquals('Sweeny', $profile->getLastName());
        $this->assertEquals('Essex', $profile->getLocation());
        $this->assertEquals('Be drunk and Merry', $profile->getSlogan());
        $this->assertEquals(new \DateTimeImmutable('2017-06-06 13:43:00'), $profile->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2017-06-06 13:43:00'), $profile->getLastModifiedDate());
    }

    public function test_fields_that_are_not_set_are_handled_gracefully()
    {
        $profile = (new Profile)
            ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
            ->setCreatedDate(new \DateTimeImmutable('2017-06-06 13:43:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-06-06 13:43:00'));

        $this->assertEquals('', $profile->getFirstName());
        $this->assertEquals('', $profile->getLastName());
        $this->assertEquals('', $profile->getLocation());
        $this->assertEquals('', $profile->getSlogan());
    }
}
