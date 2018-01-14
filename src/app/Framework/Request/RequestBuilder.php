<?php

namespace Cocktales\Framework\Request;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class RequestBuilder
{
    public static function rebuildRequest(ServerRequestInterface $request, string $body): ServerRequestInterface
    {
        return new ServerRequest(
            $request->getMethod(),
            $request->getUri()->getPath(),
            [
                'AuthorizationToken' => $request->getHeaderLine('AuthorizationToken'),
                'AuthenticationToken' => $request->getHeaderLine('AuthenticationToken')
            ],
            $body
        );
    }
}
