<?php

namespace Cocktales\Application\Http\Api\v1\Validation\Cocktail;

use Cocktales\Application\Http\Api\v1\Enum\Action;
use Cocktales\Application\Http\Api\v1\Validation\RequestBodyValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\JsendResponse\JsendError;

class CocktailRequestValidator implements RequestBodyValidator
{
    /**
     * @inheritdoc
     */
    public function validate(string $action, \stdClass $body): array
    {
        switch ($action) {
            case Action::CREATE()->getValue():
                return $this->validateCreateRequest($body);
            case Action::GET_BY_ID()->getValue():
                return $this->validateGetByIdRequest($body);
            case Action::GET_BY_INGREDIENTS():
                return $this->validateGetByIngredientsRequest($body);
            case Action::GET_BY_USER():
                return $this->validateGetByUserRequest($body);
            default:
                throw new RequestValidationException("Path /cocktail/{$action} is not valid");
        }
    }

    private function validateCreateRequest(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        if (!isset($body->cocktail)) {
            $errors[] = "Required 'cocktail' object is missing";
        }

        if (!isset($body->cocktail->name)) {
            $errors[] = "Required field 'name' is missing from 'cocktail' object";
        }

        if (!isset($body->cocktail->origin)) {
            $errors[] = "Required field 'origin' is missing from 'cocktail' object";
        }

        if (!isset($body->ingredients)) {
            $errors[] = "Required 'ingredients' object is missing";
        }

        if (!is_array($body->ingredients)) {
            $errors[] = "Required 'ingredients' object is not in the correct format: array";
        }

        if (!isset($body->instructions)) {
            $errors[] = "Required 'instructions' object is missing";
        }

        if (!is_array($body->ingredients)) {
            $errors[] = "Required 'instructions' object is not in the correct format: array";
        }

        return $errors;
    }

    private function validateGetByIdRequest(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->cocktail_id)) {
            $errors[] = "Required field 'cocktail_id' is missing";
        }

        return $errors;
    }
    
    private function validateGetByIngredientsRequest(\stdClass $body): array 
    {
        $errors = [];

        if (!isset($body->ingredients)) {
            $errors[] = "Required field 'ingredients' is missing";
        }

        return $errors;
    }
    
    private function validateGetByUserRequest(\stdClass $body): array 
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        return $errors;
    }
}