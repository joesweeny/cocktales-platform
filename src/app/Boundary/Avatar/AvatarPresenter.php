<?php

namespace Cocktales\Boundary\Avatar;

use Cocktales\Domain\Avatar\Entity\Avatar;

class AvatarPresenter
{
    public function toDto(Avatar $avatar): \stdClass
    {
        return (object) [
            'filename' => $avatar->getFilename()
        ];
    }
}
