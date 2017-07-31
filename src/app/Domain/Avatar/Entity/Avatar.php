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
     * @param string $thumbnail
     * @return Avatar
     */
    public function setThumbnail(string $thumbnail): Avatar
    {
        return $this->set('thumbnail', $thumbnail);
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->get('thumbnail');
    }

    /**
     * @param string $standard
     * @return Avatar
     */
    public function setStandard(string $standard): Avatar
    {
        return $this->set('standard', $standard);
    }

    /**
     * @return string
     */
    public function getStandard(): string
    {
        return $this->get('standard');
    }
}
