<?php

namespace Cocktales\Application\Http\Api\v1\Validation\Profile;

use Cocktales\Application\Http\Api\v1\Enum\Action;
use Cocktales\Application\Http\Api\v1\Validation\RequestBodyValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\JsendResponse\JsendError;

class ProfileRequestValidator implements RequestBodyValidator
{
    /**
     * @inheritdoc
     */
    public function validate(string $action, \stdClass $body): array
    {
        switch ($action) {
            case Action::CREATE()->getValue():
            case Action::UPDATE()->getValue():
                return $this->validateCreateOrUpdateRequest($body);
            case Action::GET()->getValue():
                return $this->validateGetRequest($body);
            default:
                throw new RequestValidationException("Path /profile/{$action} is not valid");
        }
    }

    private function validateCreateOrUpdateRequest(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        if (!isset($body->username)) {
            $errors[] = "Required field 'username' is missing";
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
}