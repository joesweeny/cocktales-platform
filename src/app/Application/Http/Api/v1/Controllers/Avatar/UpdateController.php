<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
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
        $symfonyRequest = $this->factory->createRequest($request);

        $errors = $this->validateRequest(
            $body = json_decode($symfonyRequest->getContent()),
            $avatar = $symfonyRequest->files->get('avatar')
        );

        if (!empty($errors)) {
            return new JsendBadRequestResponse($errors);
        }

        if (!$this->verifyUser($userId = $body->user_id, $authId = $request->getHeaderLine('AuthenticationToken'))) {
            $this->logError($userId, $authId);
            return new JsendErrorResponse([new JsendError('Server Unavailable')]);
        }

        try {
            $avatar = $this->bus->execute(new UpdateAvatarCommand($body->user_id, $avatar));

            return new JsendSuccessResponse([
                'avatar' => $avatar
            ]);
        } catch (NotFoundException $e) {
            return new JsendNotFoundResponse([
                    new JsendError("User ID {$body->user_id} does not exist")
                ]
            );
        }
    }

    private function verifyUser(string $userId, string $authId): bool
    {
        return $userId === $authId;
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
            $errors[] = new JsendError("Required field 'user_id' is missing");
        }

        if (!$avatar) {
            $errors[] = new JsendError("Required file 'avatar' is missing");
        }

        return $errors;
    }

    private function logError(string $userId, string $authId)
    {
        $this->logger->error("User {$authId} has attempted to update Avatar for User {$userId}");
    }
}
