<?php

namespace Cocktales\Domain\Avatar\Entity;

use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Uuid\Uuid;

class Avatar
{
    use PrivateAttributesTrait;
    use TimestampedTrait;

    /**
     * @param Uuid $userId
     * @return Avatar
     */
    public function setUserId(Uuid $userId): Avatar
    {
        return $this->set('user_id', $userId);
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return $this->get('user_id');
    }

    /**
     * @param string $filename
     * @return Avatar
     */
    public function setFilename(string $filename): Avatar
    {
        return $this->set('filename', $filename);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->get('filename', '');
    }
}
