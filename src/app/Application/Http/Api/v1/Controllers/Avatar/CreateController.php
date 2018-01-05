<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
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

        $errors = $this->validateRequest(
            $body = json_decode($request->getContent()),
            $avatar = $request->files->get('avatar')
        );

        if (!empty($errors)) {
            return new JsendErrorResponse($errors);
        }

        $avatar = $this->bus->execute(new CreateAvatarCommand($body->user_id, $avatar));

        return new JsendSuccessResponse([
            'avatar' => $avatar
        ]);
    }

    /**
     * @param mixed $body
     * @param mixed $avatar
     * @return array
     */
    private function validateRequest($body, $avatar): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = new JsendError("Required field 'User Id' is missing");
        }

        if (!$avatar) {
            $errors[] = new JsendError("Required file 'Avatar' is missing");
        }

        return $errors;
    }
}
