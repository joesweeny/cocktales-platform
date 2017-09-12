<?php

namespace Cocktales\Domain\Cocktail;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class CocktailPresenterTest extends TestCase
{
    public function test_to_dto_returns_a_std_class_object_containing_cocktail_information()
    {
        $dto = (new CocktailPresenter)->toDto((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed')->setCreatedDate(new \DateTimeImmutable('2017-03-12')));

        $this->assertInstanceOf(\stdClass::class, $dto);
        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $dto->id);
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', $dto->userId);
        $this->assertEquals('The Titty Twister', $dto->name);
        $this->assertEquals('Made in my garage when pissed', $dto->origin);
        $this->assertEquals('2017-03-12', $dto->createdAt);
    }
}
