<?php

namespace FiiSoft\Tools\Logger;

use Exception;
use Psr\Log\LogLevel;
use RuntimeException;

trait LoggedExceptionThrower
{
    use SmartLoggerHolder;
    
    /**
     * @param Exception|string $exception
     * @param bool $critical (default true)
     * @throws RuntimeException
     * @return void
     */
    final protected function throwException($exception, $critical = true)
    {
        if ($exception instanceof Exception) {
            $message = $exception->getMessage();
            if ($critical && $exception->getCode() < 1) {
                $exception = new RuntimeException($message, 1, $exception);
            }
        } else {
            $message = (string) $exception;
            $code = $critical ? 1 : 0;
            $exception = new RuntimeException($message, $code);
        }
        
        $level = $critical ? LogLevel::CRITICAL : LogLevel::ERROR;
        $this->log($message, $level);
        
        throw $exception;
    }
}