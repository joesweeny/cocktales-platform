<?php

namespace Cocktales\Framework\Middleware;

use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorHandler implements ServerMiddlewareInterface
{
    /**
     * @var ErrorResponseFactory
     */
    private $presenter;
    /**
     * @var ErrorLogger
     */
    private $logger;

    /**
     * ErrorHandler constructor.
     * @param ErrorResponseFactory $presenter
     * @param ErrorLogger $logger
     */
    public function __construct(ErrorResponseFactory $presenter, ErrorLogger $logger)
    {
        $this->presenter = $presenter;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        try {
            return $delegate->process($request);
        } catch (\Throwable $e) {
            $this->logger->log($e);
            return $this->presenter->create($e);
        }
    }
}
