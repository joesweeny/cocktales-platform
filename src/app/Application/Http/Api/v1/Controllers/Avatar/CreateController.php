<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Controller\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CreateController
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

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \LogicException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $request = $this->factory->createRequest($request);

        $body = json_decode($request->getContent());

        $avatar = $this->bus->execute(new CreateAvatarCommand($body->user_id, $request->files->get('avatar')));

        return JsendResponse::success([
            'avatar' => $avatar
        ]);
    }
}
