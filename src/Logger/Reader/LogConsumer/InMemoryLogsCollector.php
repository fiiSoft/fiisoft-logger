<?php

namespace FiiSoft\Logger\Reader\LogConsumer;

use Countable;
use FiiSoft\Logger\Reader\LogConsumer;
use LogicException;

final class InMemoryLogsCollector implements LogConsumer, Countable
{
    /** @var array */
    private $collectedLogs = [];
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function consumeLog($message, array $context = [])
    {
        $this->collectedLogs[] = [
            'message' => $message,
            'context' => $context,
        ];
    }
    
    /**
     * @return array each element is an array with keys 'message' and 'context'
     */
    public function getCollectedLogs()
    {
        return $this->collectedLogs;
    }
    
    /**
     * @return string[] collected messages
     */
    public function getCollectedMessages()
    {
        return array_map(function ($log) {return $log['message'];}, $this->collectedLogs);
    }
    
    /**
     * Purge collected logs.
     *
     * @return void
     */
    public function clear()
    {
        $this->collectedLogs = [];
    }
    
    /**
     * Count number of collected logs.
     *
     * @return int
     */
    public function count()
    {
        return count($this->collectedLogs);
    }
    
    /**
     * Send all logs to other LogConsumer.
     * Be aware that logs are removed during this process.
     *
     * @param LogConsumer $logConsumer
     * @throws LogicException
     * @return void
     */
    public function sendLogsTo(LogConsumer $logConsumer)
    {
        if ($logConsumer === $this) {
            throw new LogicException('Operation not allowed');
        }
        
        while (!empty($this->collectedLogs)) {
            $log = array_shift($this->collectedLogs);
            $logConsumer->consumeLog($log['message'], $log['context']);
        }
    }
}