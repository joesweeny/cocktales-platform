<?php

namespace Cocktales\Application\Http\Api\v1\Validation\User;

use Cocktales\Application\Http\Api\v1\Enum\Action;
use Cocktales\Application\Http\Api\v1\Validation\RequestBodyValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\JsendResponse\JsendError;

class UserRequestValidator implements RequestBodyValidator
{
    /**
     * @inheritdoc
     */
    public function validate(string $action, \stdClass $body): array
    {
        switch ($action) {
            case Action::REGISTER()->getValue():
            case Action::LOGIN()->getValue():
                return $this->validateRegisterOrLoginRequest($body);
            case Action::GET()->getValue():
                return $this->validateGetRequest($body);
            case Action::UPDATE()->getValue():
                return $this->validateUpdateRequest($body);
            default:
                throw new RequestValidationException("Path /user/{$action} is not valid");
        }
    }

    private function validateRegisterOrLoginRequest($body): array
    {
        $errors = [];

        if (!isset($body->email)) {
            $errors[] = "Required field 'email' is missing";
        }

        if (!isset($body->password)) {
            $errors[] = "Required field 'password' is missing";
        }

        return $errors;
    }

    private function validateGetRequest(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        return $errors;
    }

    private function validateUpdateRequest($body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        if (!isset($body->email)) {
            $errors[] = "Required field 'email' is missing";
        }

        if (!isset($body->password)) {
            $errors[] = "Required field 'password' is missing";
        }

        return $errors;
    }
}