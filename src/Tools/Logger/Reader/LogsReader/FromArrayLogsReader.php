<?php

namespace FiiSoft\Tools\Logger\Reader\LogsReader;

use Countable;
use FiiSoft\Tools\Logger\Reader\LogConsumer;
use FiiSoft\Tools\Logger\Reader\LogsReader;
use InvalidArgumentException;
use Psr\Log\LogLevel;

final class FromArrayLogsReader implements LogsReader, Countable
{
    /** @var string[] */
    private $logsToRead = [];
    
    /**
     * @param array $logsToRead
     * @param string|null $defaultLevel
     * @param array $defaultContext
     * @throws InvalidArgumentException
     */
    public function __construct(array $logsToRead = [], $defaultLevel = null, array $defaultContext = [])
    {
        $this->setLogsToRead($logsToRead, $defaultLevel, $defaultContext);
    }
    
    /**
     * @param array $logsToRead
     * @param string|null $defaultLevel
     * @param array $defaultContext
     * @throws InvalidArgumentException
     * @return void
     */
    public function setLogsToRead(array $logsToRead, $defaultLevel = null, array $defaultContext = [])
    {
        $this->clear();
        $this->addLogsToRead($logsToRead, $defaultLevel, $defaultContext);
    }
    
    /**
     * @param array $logsToRead
     * @param string|null $defaultLevel
     * @param array $defaultContext
     * @throws InvalidArgumentException
     * @return void
     */
    public function addLogsToRead(array $logsToRead, $defaultLevel = null, array $defaultContext = [])
    {
        foreach ($logsToRead as $log) {
            if (is_string($log)) {
                $logMessage = $log;
                $level = $defaultLevel;
                $context = $defaultContext;
            } elseif (is_array($log) && isset($log['message'])) {
                $logMessage = $log['message'];
                $level = isset($log['level']) ? $log['level'] : $defaultLevel;
                $context = !empty($log['context']) ? $log['context'] : $defaultContext;
            } else {
                throw new InvalidArgumentException('Invalid param logsToRead');
            }
            
            $this->addLogMessage($logMessage, $level, $context);
        }
    }
    
    /**
     * @param string $logMessage
     * @param string|null $level
     * @param array $context
     * @return void
     */
    public function addLogMessage($logMessage, $level = null, array $context = [])
    {
        $log = [
            'message' => (string) $logMessage,
        ];
    
        if ($level !== null) {
            $log['level'] = $level;
        }
    
        if (!empty($context)) {
            $log['context'] = $context;
        }
        
        $this->logsToRead[] = $log;
    }
    
    /**
     * @param LogConsumer $logConsumer
     * @param integer|null $maxReads number of logs to consume before return
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws InvalidArgumentException if param maxReads is invalid
     * @return void
     */
    public function read(LogConsumer $logConsumer, $maxReads = null, $timeout = 0)
    {
        if ($maxReads === null) {
            while (!empty($this->logsToRead)) {
                $this->pushNextLogToConsumer($logConsumer);
            }
        } else {
            $i = 0;
            while ($i < $maxReads && !empty($this->logsToRead)) {
                $this->pushNextLogToConsumer($logConsumer);
                ++$i;
            }
    
            if ($i === $maxReads) {
                return;
            }
        }
        
        if ($timeout > 0) {
            sleep($timeout);
        } else {
            LOOP:
            //yes - infinite loop
            goto LOOP;
        }
    }
    
    /**
     * @param LogConsumer $logConsumer
     * @return void
     */
    private function pushNextLogToConsumer(LogConsumer $logConsumer)
    {
        $log = array_shift($this->logsToRead);
        
        $message = $log['message'];
    
        if (isset($log['context'])) {
            $context = $log['context'];
        } else {
            $context = [];
        }
    
        $context['level'] = isset($log['level']) ? $log['level'] : LogLevel::DEBUG;
        
        $logConsumer->consumeLog($message, $context);
    }
    
    /**
     * Count elements not read yet.
     *
     * @return int
     */
    public function count()
    {
        return count($this->logsToRead);
    }
    
    /**
     * Removes all logs not read yet.
     *
     * @return void
     */
    public function clear()
    {
        $this->logsToRead = [];
    }
}