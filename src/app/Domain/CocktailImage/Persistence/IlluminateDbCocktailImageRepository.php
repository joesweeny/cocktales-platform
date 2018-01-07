<?php

namespace Cocktales\Domain\CocktailImage\Persistence;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Domain\CocktailImage\Hydration\Extractor;
use Cocktales\Domain\CocktailImage\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;

class IlluminateDbCocktailImageRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function insertImage(CocktailImage $image): CocktailImage
    {
        $image->setCreatedDate($this->clock->now());

        $this->connection->table('cocktail_image')->insert((array) Extractor::toRawData($image));

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function getImageByCocktailId(Uuid $cocktailId): CocktailImage
    {
        if (!$row = $this->connection->table('cocktail_image')->where('cocktail_id', $cocktailId->toBinary())->first()) {
            throw new NotFoundException("CocktailImage with cocktail ID {$cocktailId} does not exist");
        }

        return Hydrator::fromRawData($row);
    }
}