<?php

namespace Cocktales\Domain\Avatar\Hydration;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    /**
     * @param \stdClass $data
     * @return Avatar
     */
    public static function fromRawData(\stdClass $data): Avatar
    {
        return (new Avatar)
            ->setUserId(Uuid::createFromBinary($data->user_id))
            ->setThumbnail($data->thumbnail)
            ->setStandard($data->standard)
            ->setCreatedDate((new \DateTimeImmutable)->setTimestamp($data->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable)->setTimestamp($data->created_at));
    }
}
