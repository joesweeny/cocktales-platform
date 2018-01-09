<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class CreateAvatarCommand implements Command
{
    /**
     * @var string
     */
    private $user_id;
    /**
     * @var string
     */
    private $fileContents;

    /**
     * CreateAvatarCommand constructor.
     * @param string $user_id
     * @param string $fileContents
     */
    public function __construct(string $user_id, string $fileContents)
    {
        $this->user_id = $user_id;
        $this->fileContents = $fileContents;
    }

    public function getUserId(): Uuid
    {
        return new Uuid($this->user_id);
    }

    public function getFileContents(): string
    {
        return $this->fileContents;
    }
}
