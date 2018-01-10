<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Boundary\Profile\Command\UpdateProfileCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendNotFoundResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class UpdateController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $errors = $this->validateRequest($body);

        if (!empty($errors)) {
            return new JsendBadRequestResponse($errors);
        }

        if (!$this->verifyUser($userId = $body->user_id, $authId = $request->getHeaderLine('AuthenticationToken'))) {
            $this->logError($userId, $authId);
            return new JsendErrorResponse([new JsendError('You are not authorized to perform this action')]);
        }

        $data = (object) [
            'user_id' => $body->user_id,
            'username' => $body->username,
            'first_name' => $body->first_name ?? '',
            'last_name' => $body->last_name ?? '',
            'location' => $body->location ?? '',
            'slogan' => $body->slogan ?? ''
        ];

        try {
            $profile = $this->bus->execute(new UpdateProfileCommand($data));

            return new JsendSuccessResponse([
                'profile' => $profile
            ]);
        } catch (NotFoundException $e) {
            return new JsendNotFoundResponse([
                new JsendError("Profile for User ID {$body->user_id} does not exist")
            ]);
        } catch (UsernameValidationException $e) {
            return new JsendErrorResponse([
                new JsendError('Username is already taken')
            ]);
        }
    }

    private function verifyUser(string $userId, string $authId): bool
    {
        return $userId === $authId;
    }

    private function validateRequest($body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = new JsendError("Required field 'user_id' is missing");
        }

        if (!isset($body->username)) {
            $errors[] = new JsendError("Required field 'username' is missing");
        }

        return $errors;
    }

    private function logError(string $userId, string $authId)
    {
        $this->logger->error("User {$authId} has attempted to update profile for User {$userId}");
    }
}
