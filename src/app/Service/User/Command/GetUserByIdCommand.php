<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;

class GetUserByIdCommand implements Command
{
    /**
     * @var string
     */
    private $id;

    /**
     * GetUserByIdCommand constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->id;
    }
}
