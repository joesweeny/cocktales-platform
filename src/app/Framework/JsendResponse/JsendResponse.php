<?php

namespace Cocktales\Framework\JsendResponse;

use Zend\Diactoros\Response\JsonResponse;

class JsendResponse extends JsonResponse
{
    /**
     * JsendResponse constructor.
     * @param mixed $data
     * @param string $status
     * @param array $headers
     * @param int $encodingOptions
     * @internal
     * @throws \InvalidArgumentException
     */
    public function __construct($data, string $status = 'success', array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $data = (object) [
            'status' => $status,
            'data' => $data
        ];

        switch ($status) {
            case 'success':
                $statusCode = 200;
                break;
            case 'fail':
                $statusCode = 400;
                break;
            case 'error':
                $statusCode = 500;
                break;
            default:
                throw new \InvalidArgumentException("Status '$status' is not a valid Jsend status");
        }

        parent::__construct($data, $statusCode, $headers, $encodingOptions);
    }

    /**
     * @param array $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function success($data = [], array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'success', $headers, $encodingOptions);
    }

    /**
     * @param $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function fail($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'fail', $headers, $encodingOptions);
    }

    /**
     * @param $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function error($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'error', $headers, $encodingOptions);
    }
}
