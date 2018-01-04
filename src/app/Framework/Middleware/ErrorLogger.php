<?php

namespace Cocktales\Framework\Middleware;

interface ErrorLogger
{
    /**
     * @param \Throwable $exception
     * @return void
     */
    public function log(\Throwable $exception);
}
