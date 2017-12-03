<?php

namespace FiiSoft\Logger\SmartLogger;

use FiiSoft\Logger\SmartLogger;
use Psr\Log\LogLevel;

trait SmartLoggerHolder
{
    /** @var array context for logger */
    protected $logContext = [];
    
    /** @var SmartLogger */
    protected $logger;
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logDebug($message)
    {
        $this->log($message, LogLevel::DEBUG);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logInfo($message)
    {
        $this->log($message, LogLevel::INFO);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logNotice($message)
    {
        $this->log($message, LogLevel::NOTICE);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logWarning($message)
    {
        $this->log($message, LogLevel::WARNING);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logError($message)
    {
        $this->log($message, LogLevel::ERROR);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logCritical($message)
    {
        $this->log($message, LogLevel::CRITICAL);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logAlert($message)
    {
        $this->log($message, LogLevel::ALERT);
    }
    
    /**
     * @param string $message
     * @return void
     */
    final protected function logEmergency($message)
    {
        $this->log($message, LogLevel::EMERGENCY);
    }
    
    /**
     * @param string $message
     * @param string $level
     * @return void
     */
    final protected function log($message, $level)
    {
        $this->logger->log($level, $message, $this->logContext);
    }
}