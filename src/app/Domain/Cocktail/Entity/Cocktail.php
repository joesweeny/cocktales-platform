<?php

namespace Cocktales\Domain\Cocktail\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class Cocktail
{
    use PrivateAttributesTrait,
        TimestampedTrait;
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var string
     */
    private $name;

    /**
     * Cocktail constructor.
     * @param Uuid $id
     * @param Uuid $userId
     * @param string $name
     */
    public function __construct(Uuid $id, Uuid $userId, string $name)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setOrigin(string $origin): Cocktail
    {
        return $this->set('origin', $origin);
    }

    public function getOrigin(): string
    {
        return $this->get('origin', '');
    }

    public function setIngredients(Collection $ingredients): Cocktail
    {
        return $this->set('ingredients', $ingredients);
    }

    public function getIngredients(): Collection
    {
        return $this->get('ingredients', new Collection());
    }

    public function setInstructions(Collection $instructions): Cocktail
    {
        return $this->set('instructions', $instructions);
    }

    public function getInstructions(): Collection
    {
        return $this->get('instructions', new Collection());
    }
}
