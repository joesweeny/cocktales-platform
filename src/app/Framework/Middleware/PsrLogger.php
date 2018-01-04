<?php

namespace Cocktales\Framework\Middleware;

use Psr\Log\LoggerInterface;

class PsrLogger implements ErrorLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var callable
     */
    private $logLevelCallback;

    /**
     * PsrLogger constructor.
     * @param LoggerInterface $logger
     * @param callable $logLevelCallback
     *  When an exception occurs, this callable will be called and passed a single argument (the
     *  Throwable). This callable should return a string which is a log level that can be passed
     *  to the PSR LoggerInterface.
     */
    public function __construct(LoggerInterface $logger, callable $logLevelCallback = null)
    {
        $this->logger = $logger;
        $this->logLevelCallback = $logLevelCallback ?: function () {
            return 'error';
        };
    }

    public function log(\Throwable $exception)
    {
        $class = get_class($exception);
        $message = "$class caught with message '{$exception->getMessage()}'";

        $context = ['exception' => $exception];

        $level = call_user_func($this->logLevelCallback, $exception);

        $this->logger->log($level, $message, $context);
    }
}
