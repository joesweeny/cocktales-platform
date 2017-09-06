<?php

namespace Cocktales\Domain\CocktailIngredient\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;

class CocktailIngredient
{
    use PrivateAttributesTrait,
        TimestampedTrait;
    /**
     * @var Uuid
     */
    private $cocktailId;
    /**
     * @var Uuid
     */
    private $ingredientId;
    /**
     * @var int
     */
    private $orderNumber;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var string
     */
    private $measurement;

    /**
     * CocktailIngredient constructor.
     * @param Uuid $cocktailId
     * @param Uuid $ingredientId
     * @param int $orderNumber
     * @param int $quantity
     * @param string $measurement
     */
    public function __construct(Uuid $cocktailId, Uuid $ingredientId, int $orderNumber, int $quantity, string $measurement)
    {
        $this->cocktailId = $cocktailId;
        $this->ingredientId = $ingredientId;
        $this->orderNumber = $orderNumber;
        $this->quantity = $quantity;
        $this->measurement = $measurement;
    }

    public function getCocktailId(): Uuid
    {
        return $this->cocktailId;
    }

    public function getIngredientId(): Uuid
    {
        return $this->ingredientId;
    }

    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getMeasurement(): string
    {
        return $this->measurement;
    }
}
