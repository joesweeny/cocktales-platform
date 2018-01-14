<?php

namespace Cocktales\Domain\CocktailImage;

use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class CocktailImageOrchestrator
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * AvatarOrchestrator constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Uuid $cocktailId
     * @param string $contents
     */
    public function createImage(Uuid $cocktailId, string $contents): void
    {
        $this->filesystem->put("/cocktail/image/{$cocktailId}", $contents);
    }

    /**
     * @param Uuid $cocktailId
     * @return string
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getImageByCocktailId(Uuid $cocktailId): string
    {
        if (!$this->filesystem->has("/cocktail/image/{$cocktailId}")) {
            throw new NotFoundException("Image for Cocktail {$cocktailId} does not exist");
        }

        try {
            if (!$content = $this->filesystem->read("/cocktail/image/{$cocktailId}")) {
                throw new NotFoundException("Unable to retrieve image file contents for Cocktail {$cocktailId}");
            }
        } catch (FileNotFoundException $e) {
            throw new NotFoundException("Image for Cocktail {$cocktailId} does not exist");
        }

        return $content;
    }

    /**
     * @param Uuid $cocktailId
     * @param string $contents
     * @return void
     */
    public function updateImage(Uuid $cocktailId, string $contents): void
    {
        $this->filesystem->put("/cocktail/image/{$cocktailId}", $contents);
    }

    public function imageExists(Uuid $cocktailId): bool
    {
        return $this->filesystem->has("/cocktail/image/{$cocktailId}");
    }
}
