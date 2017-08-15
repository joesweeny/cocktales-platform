<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateAvatarCommand implements Command
{
    /**
     * @var string
     */
    private $user_id;
    /**
     * @var UploadedFile
     */
    private $file;


    /**
     * CreateAvatarCommand constructor.
     * @param string $user_id
     * @param UploadedFile $file
     */
    public function __construct(string $user_id, UploadedFile $file)
    {
        $this->user_id = $user_id;
        $this->file = $file;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return new Uuid($this->user_id);
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
