<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Framework\Exception\NotAuthorizedException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\TextResponse;

class HtmlErrorResponseFactory implements ErrorResponseFactory
{
    /**
     * @param \Throwable $exception
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     * @todo Replace with more user-friendly HTML response
     */
    public function create(\Throwable $exception): ResponseInterface
    {
        if ($exception instanceof NotFoundException) {
            return new TextResponse(
                'Page not found',
                404
            );
        }

        if ($exception instanceof NotAuthorizedException) {
            return new JsendFailResponse([
                    new JsendError('You are not authorized to perform this action')
                ]
            );
        }

        return new TextResponse(
            'Server Unavailable',
            500
        );
    }
}
