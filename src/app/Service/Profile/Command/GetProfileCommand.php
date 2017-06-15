<?php

namespace Cocktales\Service\Profile\Command;

use Cocktales\Framework\CommandBus\Command;

class GetProfileCommand implements Command
{
    /**
     * @var string
     */
    private $id;

    /**
     * GetProfileCommand constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function userId()
    {
        return $this->id;
    }
}
