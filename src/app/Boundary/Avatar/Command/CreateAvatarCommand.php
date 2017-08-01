<?php

namespace Cocktales\Boundary\Avatar\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class CreateAvatarCommand implements Command
{
    /**
     * @var string
     */
    private $thumbnail;
    /**
     * @var string
     */
    private $standardSize;
    /**
     * @var string
     */
    private $user_id;

    /**
     * CreateAvatarCommand constructor.
     * @param string $user_id
     * @param string $thumbnail
     * @param string $standardSize
     */
    public function __construct(string $user_id, string $thumbnail, string $standardSize)
    {
        $this->user_id = $user_id;
        $this->thumbnail = $thumbnail;
        $this->standardSize = $standardSize;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return new Uuid($this->user_id);
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @return string
     */
    public function getStandardSize(): string
    {
        return $this->standardSize;
    }
}
