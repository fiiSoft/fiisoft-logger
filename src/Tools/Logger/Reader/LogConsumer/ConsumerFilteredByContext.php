<?php

namespace FiiSoft\Tools\Logger\Reader\LogConsumer;

use FiiSoft\Tools\Logger\Reader\LogConsumer;

final class ConsumerFilteredByContext implements LogConsumer
{
    /** @var LogConsumer */
    private $consumer;
    
    /** @var array */
    private $withContext;
    
    /**
     * @param LogConsumer $consumer
     * @param array $withContext
     */
    public function __construct(LogConsumer $consumer, array $withContext)
    {
        $this->consumer = $consumer;
        $this->withContext = $withContext;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function consumeLog($message, array $context = [])
    {
        foreach ($this->withContext as $key => $value) {
            if (!isset($context[$key]) || $context[$key] !== $value) {
                return;
            }
        }
        
        $this->consumer->consumeLog($message, $context);
    }
}