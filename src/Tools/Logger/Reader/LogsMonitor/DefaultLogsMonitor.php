<?php

namespace FiiSoft\Tools\Logger\Reader\LogsMonitor;

use BadMethodCallException;
use FiiSoft\Tools\Logger\Reader\AbstractLogsMonitor;
use FiiSoft\Tools\Logger\Reader\LogsReader;
use InvalidArgumentException;

final class DefaultLogsMonitor extends AbstractLogsMonitor
{
    /** @var LogsReader */
    private $logsReader;
    
    /**
     * The list of Levels must be ordered from the least to the most significant (if provided).
     *
     * @param LogsReader $logsReader
     * @param array $levels use these levels instead of default if not empty
     */
    public function __construct(LogsReader $logsReader, array $levels = [])
    {
        $this->logsReader = $logsReader;
        $this->levels = $levels;
    }
    
    /**
     * Start to sending logs to OutputWriter, which has to be set before call this method.
     * If param maxNum is provided, then only given number of messages will be transferred to OutputWriter.
     *
     * @param integer|null $maxNum number of logs to read before return; must be >= 1
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws BadMethodCallException if OutputWriter was not set before this method is call
     * @throws InvalidArgumentException if param maxNum is invalid
     * @return void
     */
    public function start($maxNum = null, $timeout = 0)
    {
        $this->assertParamsAreValid($maxNum, $timeout);
        $this->prepareConsumer();
        
        $this->logsReader->read($this->consumer, $maxNum, $timeout);
    }
}