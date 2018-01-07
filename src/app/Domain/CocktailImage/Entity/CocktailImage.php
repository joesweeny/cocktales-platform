<?php

namespace Cocktales\Domain\CocktailImage\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;

class CocktailImage
{
    use PrivateAttributesTrait,
        TimestampedTrait;
    /**
     * @var Uuid
     */
    private $cocktailId;
    /**
     * @var string
     */
    private $filename;

    public function __construct(Uuid $cocktailId, string $filename)
    {
        $this->cocktailId = $cocktailId;
        $this->filename = $filename;
    }

    public function getCocktailId(): Uuid
    {
        return $this->cocktailId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
