<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Boundary\Profile\Command\CreateProfileCommand;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
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
            $profile = $this->bus->execute(new CreateProfileCommand($data));

            return JsendResponse::success([
                'profile' => $profile
            ]);
        } catch (UsernameValidationException $e) {
            return JsendResponse::fail([
                'error' => 'Username is already taken'
            ]);
        }
    }
}
