<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class AvatarOrchestrator
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
     * @param Uuid $userId
     * @param string $contents
     */
    public function createAvatar(Uuid $userId, string $contents): void
    {
        $this->filesystem->put("/avatar/{$userId}", $contents);
    }

    /**
     * @param Uuid $userId
     * @return string
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getAvatarByUserId(Uuid $userId): string
    {
        if (!$this->filesystem->has("/avatar/{$userId}")) {
            throw new NotFoundException("Avatar for User {$userId} does not exist");
        }

        try {
            if (!$content = $this->filesystem->read("/avatar/{$userId}")) {
                throw new NotFoundException("Unable to retrieve avatar file contents for User {$userId}");
            }
        } catch (FileNotFoundException $e) {
            throw new NotFoundException("Avatar for User {$userId} does not exist");
        }

        return $content;
    }

    /**
     * @param Uuid $userId
     * @param string $contents
     * @return void
     */
    public function updateAvatar(Uuid $userId, string $contents): void
    {
        $this->filesystem->put("/avatar/{$userId}", $contents);
    }
}
