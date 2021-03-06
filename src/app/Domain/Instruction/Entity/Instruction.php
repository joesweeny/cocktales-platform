<?php

namespace Cocktales\Domain\Instruction\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;

class Instruction
{
    use PrivateAttributesTrait,
        TimestampedTrait;

    /**
     * @var Uuid
     */
    private $cocktailId;
    /**
     * @var int
     */
    private $orderNumber;
    /**
     * @var string
     */
    private $text;

    /**
     * Instruction constructor.
     * @param Uuid $cocktailId
     * @param int $orderNumber
     * @param string $text
     */
    public function __construct(Uuid $cocktailId, int $orderNumber, string $text)
    {
        $this->cocktailId = $cocktailId;
        $this->orderNumber = $orderNumber;
        $this->text = $text;
    }

    /**
     * @return Uuid
     */
    public function getCocktailId(): Uuid
    {
        return $this->cocktailId;
    }

    /**
     * @return int
     */
    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
