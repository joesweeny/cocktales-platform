<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\UpdateUserCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Framework\Exception\UserPasswordValidationException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
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
            'id' => $body->user_id,
            'email' => $body->email,
            'oldPassword' => $body->oldPassword ?? '',
            'newPassword' => $body->newPassword ?? ''
        ];

        try {
            $user = $this->bus->execute(new UpdateUserCommand($data));

            return new JsendSuccessResponse([
                'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return new JsendErrorResponse([
                new JsendError('Unable to process request - please try again')
            ]);
        } catch (UserEmailValidationException $e) {
            return new JsendErrorResponse([
                new JsendError('A user has already registered with this email address')
            ]);
        } catch (UserPasswordValidationException $e) {
            return new JsendErrorResponse([
                new JsendError('Password does not match the password on record - please try again')
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

        if (!isset($body->email)) {
            $errors[] = new JsendError("Required field 'email' is missing");
        }

        return $errors;
    }

    private function logError(string $userId, string $authId)
    {
        $this->logger->error("User {$authId} has attempted to update profile for User {$userId}");
    }
}
