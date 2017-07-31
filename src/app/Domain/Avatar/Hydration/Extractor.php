<?php

namespace Cocktales\Domain\Avatar\Hydration;

use Cocktales\Domain\Avatar\Entity\Avatar;

class Extractor
{
    public static function toRawData(Avatar $avatar): \stdClass
    {
        return (object) [
            'user_id' => $avatar->getUserId()->toBinary(),
            'thumbnail' => $avatar->getThumbnail(),
            'standard' => $avatar->getStandard(),
            'created_at' => $avatar->getCreatedDate()->getTimestamp(),
            'updated_at' => $avatar->getLastModifiedDate()->getTimestamp()
        ];
    }
}
