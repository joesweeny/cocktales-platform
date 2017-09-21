<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Boundary\Cocktail\Creation\Transformer;
use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Creation\Mixer;

class CreateCocktailCommandHandler
{
    /**
     * @var Transformer
     */
    private $transformer;
    /**
     * @var CocktailPresenter
     */
    private $presenter;
    /**
     * @var Mixer
     */
    private $mixer;

    /**
     * CreateCocktailCommandHandler constructor.
     * @param Mixer $mixer
     * @param Transformer $transformer
     * @param CocktailPresenter $presenter
     */
    public function __construct(Mixer $mixer, Transformer $transformer, CocktailPresenter $presenter)
    {
        $this->mixer = $mixer;
        $this->transformer = $transformer;
        $this->presenter = $presenter;
    }

    /**
     * @param CreateCocktailCommand $command
     * @throws \Cocktales\Domain\CocktailIngredient\Exception\RepositoryException
     * @throws \Cocktales\Domain\Cocktail\Exception\DuplicateNameException
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     * @return \stdClass
     */
    public function handle(CreateCocktailCommand $command): \stdClass
    {
        $cocktail = $this->transformer->toCocktail($command->getCocktail(), $command->getUserId());

        $ingredients = $this->transformer->toCocktailIngredients($command->getIngredients(), $cocktail->getId());

        $instructions = $this->transformer->toCocktailInstructions($command->getInstructions(), $cocktail->getId());

        return $this->presenter->toDto($this->mixer->createCocktail(
            $cocktail->setIngredients($ingredients)->setInstructions($instructions)
        ));
    }
}
