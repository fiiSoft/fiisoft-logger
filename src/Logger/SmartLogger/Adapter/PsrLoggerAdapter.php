<?php

namespace FiiSoft\Logger\SmartLogger\Adapter;

use FiiSoft\Logger\SmartLogger\AbstractSmartLogger;
use Psr\Log\LoggerInterface;

final class PsrLoggerAdapter extends AbstractSmartLogger
{
    /** @var LoggerInterface */
    private $logger;
    
    /**
     * @param LoggerInterface $logger decorated Psr logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if (isset($this->levels[$level]) && $this->levels[$level] < $this->minLevel) {
            return;
        }
    
        if (!empty($this->context) || !empty($context)) {
            $context = array_merge($this->context, $context);
        }
        
        if ($this->prefix !== '') {
            $this->logger->log($level, $this->prefix . $message, $context);
        } else {
            $this->logger->log($level, $message, $context);
        }
    }
}