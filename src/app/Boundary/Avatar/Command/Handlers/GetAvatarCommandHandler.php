<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\GetAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;

class GetAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;

    /**
     * GetAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     */
    public function __construct(AvatarOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GetAvatarCommand $command
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @return string
     */
    public function handle(GetAvatarCommand $command): string
    {
        return $this->orchestrator->getAvatarByUserId($command->getUserId());
    }
}
