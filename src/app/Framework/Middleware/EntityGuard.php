<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotAuthorizedException;
use Cocktales\Framework\Request\RequestBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Used to guard users from updating entities that do not belong to them
 */
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
            if ($cocktail->cocktail->user_id !== $userId) {
                $this->logError($uri->getPath(), $userId, $authId, $cocktailId);
                throw new NotAuthorizedException('You are not authorized to perform this action');
            }
        }

        return $delegate->process(RequestBuilder::rebuildRequest($request, json_encode($body)));
    }

    /**
     * @param string $path
     * @param string $userId
     * @param string $authId
     * @param string $entityId
     */
    private function logError(string $path, string $userId, string $authId, string $entityId = '')
    {
        $this->logger->error('An attempt has been made to create or update a record that does not belong to the user', [
            'Auth ID' => $authId,
            'User ID' => $userId,
            'Entity ID' => $entityId,
            'Path' => $path
        ]);
    }
}