<?php

namespace Cocktales\Framework\Controller;

use Cocktales\Framework\CommandBus\CommandBus;

trait ControllerService
{

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * ControllerService constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }
}
