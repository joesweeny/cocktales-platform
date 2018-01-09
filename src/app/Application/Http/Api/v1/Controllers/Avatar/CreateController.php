<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use League\Flysystem\FileExistsException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CreateController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
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

        $this->bus->execute(new CreateAvatarCommand($body->user_id, $avatar));

        return new JsendSuccessResponse;
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
