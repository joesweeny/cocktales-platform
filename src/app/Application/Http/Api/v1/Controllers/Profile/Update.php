<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Domain\Profile\Hydration\Hydrator;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Service\Profile\Command\UpdateProfileCommand;
use Psr\Http\Message\ServerRequestInterface;

class Update
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \RuntimeException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

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

            return JsendResponse::success([
                'profile' => Hydrator::toPublicViewableData($profile)
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to process request - please try again'
            ]);
        } catch (UsernameValidationException $e) {
            return JsendResponse::fail([
                'error' => 'Username is already taken'
            ]);
        }
    }
}
