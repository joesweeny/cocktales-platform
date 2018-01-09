<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendNotFoundResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(HttpFoundationFactory $factory, CommandBus $bus, LoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $errors = $this->validateRequest($body);

        if (!empty($errors)) {
            return new JsendBadRequestResponse($errors);
        }

        if (($userId = $body->user_id) !== ($authId = $request->getHeaderLine('AuthenticationToken'))) {
            $this->logError($userId, $authId);
            return new JsendFailResponse([new JsendError('You are not authorized to perform this action')]);
        }

        $avatar = $body->format === 'base64' ? base64_decode($body->avatar) : $body->avatar;

        $this->bus->execute(new UpdateAvatarCommand($body->user_id, $avatar));

        return new JsendSuccessResponse();
    }

    /**
     * @param mixed $body
     * @return array
     */
    private function validateRequest($body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = new JsendError("Required field 'user_id' is missing");
        }

        if (!$body->avatar) {
            $errors[] = new JsendError("Required image 'avatar' is missing");
        }

        if (!$body->format) {
            $errors[] = new JsendError("Required field 'format' is missing");
        }

        return $errors;
    }

    private function logError(string $userId, string $authId)
    {
        $this->logger->error("User {$authId} has attempted to update Avatar for User {$userId}");
    }
}
