<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class UpdateAvatarCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var string
     */
    private $fileContents;

    /**
     * UpdateAvatarCommand constructor.
     * @param string $userId
     * @param string $fileContents
     */
    public function __construct(string $userId, string $fileContents)
    {
        $this->userId = $userId;
        $this->fileContents = $fileContents;
    }

    public function getUserId(): Uuid
    {
        return new Uuid($this->userId);
    }

    public function getFileContents(): string
    {
        return $this->fileContents;
    }
}
