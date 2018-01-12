<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Application\Http\Api\v1\Validation\ValidatorResolver;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\Exception\UnprocessableEntityException;
use Cocktales\Framework\Request\RequestBuilder;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestGuard implements ServerMiddlewareInterface
{
    const BASE_PATH = '/api/v1/';
    /**
     * @var ValidatorResolver
     */
    private $resolver;

    public function __construct(ValidatorResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     * @throws \RuntimeException
     * @throws \Cocktales\Framework\Exception\UnprocessableEntityException
     * @throws \Cocktales\Framework\Exception\RequestValidationException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $uri = $request->getUri();

        // Stratigility mutates the `Uri` object before passing it to middleware by
        // removing the path used to bind the middleware. If the OriginalMessages
        // middleware is used we can access the originalUri attribute.
        if ($request->getAttribute('originalUri')) {
            $uri = $request->getAttribute('originalUri');
        }

        $bits = explode('/', $uri->getPath());
        $entityReference = $bits[3];
        $action = $bits[4];
        $body = json_decode($request->getBody()->getContents());

        if (!$body) {
            throw new RequestValidationException('No body in request or body is in an incorrect format');
        }

        $errors = $this->resolver->resolve($entityReference)->validate($action, $body);

        if (!empty($errors)) {
            throw new UnprocessableEntityException(implode(',', $errors));
        }

        return $delegate->process(RequestBuilder::rebuildRequest($request, json_encode($body)));
    }
}
