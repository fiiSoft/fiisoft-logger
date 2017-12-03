<?php

namespace FiiSoft\Logger\Reader\LogsMonitor;

use BadMethodCallException;
use FiiSoft\Logger\Reader\LogsMonitor;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use InvalidArgumentException;

final class NullLogsMonitor implements LogsMonitor
{
    /** @var OutputWriter */
    private $outputWriter;
    
    /** @var array */
    private $levels = [];
    
    /**
     * Allows to redefine levels (if different from default).
     * The list of Levels must be ordered from the most to the least significant.
     * Instead of call this method, new levels can be set by pass them as second argument to filterByLevels.
     *
     * @param array $levels
     * @return $this fluent interface
     */
    public function setLevels(array $levels)
    {
        $this->levels = $levels;
        return $this;
    }
    
    /**
     * List of levels that this LogsMonitor knows and can filter by one of them.
     *
     * @return array
     */
    public function getLevels()
    {
        return $this->levels;
    }
    
    /**
     * One can set minimum level of logs that will be streamed to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supposed to be streamed.
     *
     * If second param is not empty then use this set of levels instead of defaults.
     * The list of Levels must be ordered from the most to the least significant.
     *
     * @param string $level
     * @param array $levels
     * @return $this fluent interface
     */
    public function filterByLevel($level, array $levels = [])
    {
        if (!empty($levels)) {
            $this->levels = $levels;
        }
        
        return $this;
    }
    
    /**
     * One can set context that is required to stream logs to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supposed to be streamed.
     *
     * @param array $context
     * @return $this fluent interface
     */
    public function filterByContext(array $context)
    {
        return $this;
    }
    
    /**
     * Call of this method is required before logs are streamed to OutputWriter.
     *
     * @param OutputWriter $outputWriter
     * @return $this fluent interface
     */
    public function setOutputWriter(OutputWriter $outputWriter)
    {
        $this->outputWriter = $outputWriter;
        return $this;
    }
    
    /**
     * Start to sending logs to OutputWriter, which has to be set before call this method.
     * If param maxNum is provided, then only given number of messages will be transferred to OutputWriter.
     *
     * @param integer|null $maxNum number of logs to read before return; must be >= 1
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws BadMethodCallException if OutputWriter was not set before this method is call
     * @throws InvalidArgumentException if param maxNum or timeout is invalid
     * @return void
     */
    public function start($maxNum = null, $timeout = 0)
    {
        if ($maxNum !== null && (!is_int($maxNum) || $maxNum < 1)) {
            throw new InvalidArgumentException('Invalid parameter maxNum');
        } elseif (!is_int($timeout) || $timeout < 0) {
            throw new InvalidArgumentException('Invalid parameter timeout');
        } elseif (!$this->outputWriter) {
            throw new BadMethodCallException('OutputWriter is not set in '.get_class($this));
        }
    
        if ($maxNum) {
            //simulate return from method after maxNum number of logs was read
            return;
        }
    
        if ($timeout) {
            //simulate waiting for messages
            sleep($timeout);
            return;
        }
    
        INFINITE_LOOP:
            //simulate reading logs in infinite loop like real implementations
        goto INFINITE_LOOP;
    }
}