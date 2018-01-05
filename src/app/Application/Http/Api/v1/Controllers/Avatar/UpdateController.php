<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class UpdateController
{
    /**
     * @var HttpFoundationFactory
     */
    private $factory;
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * CreateController constructor.
     * @param HttpFoundationFactory $factory
     * @param CommandBus $bus
     */
    public function __construct(HttpFoundationFactory $factory, CommandBus $bus)
    {
        $this->factory = $factory;
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $request = $this->factory->createRequest($request);

        $body = json_decode($request->getContent());

        $avatar = $this->bus->execute(new UpdateAvatarCommand($body->user_id, $request->files->get('avatar')));

        return JsendResponse::success([
            'avatar' => $avatar
        ]);
    }
}
