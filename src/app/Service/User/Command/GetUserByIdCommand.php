<?php

namespace Cocktales\Service\User\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

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

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return new Uuid($this->id);
    }
}
