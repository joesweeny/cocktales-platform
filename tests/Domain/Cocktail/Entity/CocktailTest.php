<?php

namespace Cocktales\Domain\Cocktail\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class CocktailTest extends TestCase
{
    public function test_properties_set_on_cocktail_entity()
    {
        $cocktail = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))
            ->setOrigin('Made in my garage when pissed')
            ->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
            ->setMatchIngredientCount(5);

        $this->assertInstanceOf(Cocktail::class, $cocktail);
        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) $cocktail->getId());
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $cocktail->getUserId());
        $this->assertEquals('The Titty Twister', $cocktail->getName());
        $this->assertEquals('Made in my garage when pissed', $cocktail->getOrigin());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $cocktail->getCreatedDate());
        $this->assertEquals(5, $cocktail->getMatchingIngredientCount());
    }

    public function test_get_origin_returns_an_empty_string_if_not_set()
    {
        $cocktail = new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        );

        $this->assertEquals('', $cocktail->getOrigin());
    }
}
