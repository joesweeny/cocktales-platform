<?php

namespace Cocktales\Domain\Avatar\Hydration;

use Cocktales\Domain\Avatar\Entity\Avatar;

class Extractor
{
    /**
     * @param Avatar $avatar
     * @return \stdClass
     */
    public static function toRawData(Avatar $avatar): \stdClass
    {
        return (object) [
            'user_id' => $avatar->getUserId()->toBinary(),
            'filename' => $avatar->getFilename(),
            'created_at' => $avatar->getCreatedDate()->getTimestamp(),
            'updated_at' => $avatar->getLastModifiedDate()->getTimestamp()
        ];
    }
}
