<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateAvatarCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * UpdateAvatarCommand constructor.
     * @param string $userId
     * @param UploadedFile $file
     */
    public function __construct(string $userId, UploadedFile $file)
    {
        $this->userId = new Uuid($userId);
        $this->file = $file;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
