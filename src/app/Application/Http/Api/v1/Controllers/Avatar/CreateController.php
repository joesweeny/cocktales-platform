<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CreateController
{
    use ControllerService;
    /**
     * @var HttpFoundationFactory
     */
    private $factory;

    /**
     * CreateController constructor.
     * @param HttpFoundationFactory $factory
     */
    public function __construct(HttpFoundationFactory $factory)
    {
//        parent::__construct($bus);
        $this->factory = $factory;
    }

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $file = $this->factory->createRequest($request)->get('avatar');

        $body = json_decode($request->getBody()->getContents());

        $this->bus->execute(new CreateAvatarCommand($body->user_id, $file[0]));

        return JsendResponse::success();
    }
}
