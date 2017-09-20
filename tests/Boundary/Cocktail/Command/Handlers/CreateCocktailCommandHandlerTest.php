<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Boundary\Cocktail\Creation\Transformer;
use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Creation\Mixer;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CreateCocktailCommandHandlerTest extends TestCase
{
    public function test_handle_successfully_creates_a_new_cocktail()
    {
        /** @var Mixer $mixer */
        $mixer = $this->prophesize(Mixer::class);
        /** @var Transformer $transformer */
        $transformer = $this->prophesize(Transformer::class);
        /** @var CocktailPresenter $presenter */
        $presenter = $this->prophesize(CocktailPresenter::class);
        $handler = new CreateCocktailCommandHandler(
            $mixer->reveal(),
            $transformer->reveal(),
            $presenter->reveal()
        );

        $command = new CreateCocktailCommand(
            '83e0e8ff-66fe-4e35-8171-e7b701558209',
            (object) [
                'id' => 'eef97068-5f75-4129-957b-6d72499a3b95',
                'name' => 'Woo Woo',
                'origin' => 'Cult classic'
            ],
            [
                (object) [
                    'ingredientId' => '73f261d9-234e-4501-a5dc-8f4f0bc0623a',
                    'orderNumber' => 1,
                    'quantity' => 50,
                    'measurement' => 'ml'
                ],
                (object) [
                    'ingredientId' => '2ad51a5e-3b33-40f9-9143-ea261531ba2d',
                    'orderNumber' => 2,
                    'quantity' => 10,
                    'measurement' => 'oz'
                ]
            ],
            [
                (object) [
                    'orderNumber' => 4,
                    'text' => 'Shake well'
                ],
                (object) [
                    'orderNumber' => 5,
                    'text' => 'Pour'
                ]
            ]
        );

        $transformer->toCocktail($command->getCocktail(), $command->getUserId())->willReturn(
            $cocktail = (new Cocktail(
                new Uuid('eef97068-5f75-4129-957b-6d72499a3b95'),
                new Uuid('83e0e8ff-66fe-4e35-8171-e7b701558209'),
                'Woo Woo'
            ))->setOrigin('Cult Classic')
        );

        $transformer->toCocktailIngredients($command->getIngredients(), $cocktail->getId())->willReturn(
            $ingredients = new Collection([
                new CocktailIngredient(
                    new Uuid('eef97068-5f75-4129-957b-6d72499a3b95'),
                    new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
                    1,
                    50,
                    'ml'
                ),
                new CocktailIngredient(
                    new Uuid('eef97068-5f75-4129-957b-6d72499a3b95'),
                    new Uuid('2ad51a5e-3b33-40f9-9143-ea261531ba2d'),
                    1,
                    2,
                    'oz'
                )
            ])
        );
        $transformer->toCocktailInstructions($command->getInstructions(), $cocktail->getId())->willReturn(
            $instructions = new Collection([
                new Instruction(
                    new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                    1,
                    'Pour into glass'
                ),
                new Instruction(
                    new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                    1,
                    'Pour into glass'
                )
            ])
        );

        $mixer->createCocktail($cocktail->setIngredients($ingredients)->setInstructions($instructions))->willReturn(
            $cocktail = (new Cocktail(
                new Uuid('eef97068-5f75-4129-957b-6d72499a3b95'),
                new Uuid('83e0e8ff-66fe-4e35-8171-e7b701558209'),
                'Woo Woo'
            ))->setOrigin('Cult Classic'));

        $presenter->toDto($cocktail)->shouldBeCalled();

        $handler->handle($command);
    }
}
