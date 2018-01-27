<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Framework\Exception\NotAuthenticatedException;
use Cocktales\Framework\Exception\NotAuthorizedException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\Exception\UnprocessableEntityException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Psr\Http\Message\ResponseInterface;

class JsendResponseFactory implements ErrorResponseFactory
{
    /**
     * @param \Throwable $exception
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function create(\Throwable $exception): ResponseInterface
    {
        if ($exception instanceof NotFoundException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage())
            ]))->withStatus(404);
        }

        if ($exception instanceof NotAuthorizedException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage())
            ]))->withStatus(401);
        }

        if ($exception instanceof NotAuthenticatedException) {
            return (new JsendFailResponse([
                new JsendError($exception->getMessage())
            ]))->withStatus(401);
        }

        if ($exception instanceof RequestValidationException) {
            return (new JsendFailResponse(
                $this->formatJsendErrors($exception)
            ))->withStatus(400);
        }

        if ($exception instanceof UnprocessableEntityException) {
            return (new JsendFailResponse(
                $this->formatJsendErrors($exception)
            ))->withStatus(422);
        }

        return new JsendErrorResponse([new JsendError('Service is unavailable')]);
    }

    private function formatJsendErrors(\Exception $exception): array
    {
        $errors = explode(',', $exception->getMessage());

        $jsendErrors = [];

        foreach ($errors as $error) {
            $jsendErrors[] = new JsendError($error);
        }

        return $jsendErrors;
    }
}