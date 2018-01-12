<?php

namespace Cocktales\Application\Http\Api\v1\Validation;

use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\JsendResponse\JsendError;

interface RequestBodyValidator
{
    /**
     * Receives an action i.e. 'create' or 'update' and request body then validates the user input
     * is as expected. Any errors are returned in the return array in JsendError format
     *
     * @param string $action
     * @param \stdClass $body
     * @throws RequestValidationException
     * @return array|JsendError
     */
    public function validate(string $action, \stdClass $body): array;
}
