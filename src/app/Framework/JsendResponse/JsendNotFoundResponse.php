<?php

namespace Cocktales\Framework\JsendResponse;

class JsendNotFoundResponse extends JsendResponse
{
    public function __construct(array $errors = [], array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        self::validateErrors($errors);

        parent::__construct([
            'errors' => $errors
        ], 'not_found', $headers, $encodingOptions);
    }

    private static function validateErrors(array $errors)
    {
        foreach ($errors as $error) {
            if (!$error instanceof JsendError) {
                throw new \InvalidArgumentException('First argument passed to JsendFailResponse::__construct must be an array of JsendError objects');
            }
        }
    }
}
