<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotAuthorizedException;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class EntityGuard implements ServerMiddlewareInterface
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CommandBus $bus, LoggerInterface $logger)
    {
        $this->bus = $bus;
        $this->logger = $logger;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     * @throws \Cocktales\Framework\Exception\NotAuthorizedException
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

        $path = substr($uri->getPath(), strrpos($uri->getPath(), '/') + 1);

        if ($path !== 'create' && $path !== 'update') {
            return $delegate->process($request);
        }

        $body = json_decode($request->getBody()->getContents());

        $authId = $request->getHeaderLine('AuthenticationToken') ?? '';
        $userId = $body->user_id ?? '';
        $cocktailId = $body->cocktail_id ?? '';

        if ($authId !== $userId) {
            $this->logError($uri->getPath(), $userId, $authId);
            throw new NotAuthorizedException('You are not authorized to perform this action');
        }

        if ($cocktailId && $path === 'update') {
            $cocktail = $this->bus->execute(new GetCocktailByIdCommand($cocktailId));
            if ($cocktail->cocktail->userId !== $userId) {
                $this->logError($uri->getPath(), $userId, $authId);
                throw new NotAuthorizedException('You are not authorized to perform this action');
            }
        }

        return $delegate->process($this->buildNewRequest($request, json_encode($body)));
    }


    private function buildNewRequest(ServerRequestInterface $request, string $body): ServerRequestInterface
    {
        return new ServerRequest(
            $request->getMethod(),
            $request->getUri()->getPath(),
            [
                'AuthorizationToken' => $request->getHeaderLine('AuthenticationToken'),
                'AuthenticationToken' => $request->getHeaderLine('AuthenticationToken')
            ],
            $body
        );
    }

    /**
     * @param string $path
     * @param string $userId
     * @param string $authId
     */
    private function logError(string $path, string $userId, string $authId)
    {
        $this->logger->error('An attempt has been made to create or update a record that does not belong to the user', [
            'Auth ID' => $authId,
            'User ID' => $userId,
            'Path' => $path
        ]);
    }
}