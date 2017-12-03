<?php

namespace FiiSoft\Logger\Reader\LogsMonitor;

use BadMethodCallException;
use FiiSoft\Logger\Reader\LogConsumer;
use FiiSoft\Logger\Reader\LogConsumer\ConsumerFilteredByContext;
use FiiSoft\Logger\Reader\LogConsumer\ConsumerFilteredByLevel;
use FiiSoft\Logger\Reader\LogConsumer\LogToOutputSender;
use FiiSoft\Logger\Reader\LogsMonitor;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use InvalidArgumentException;

abstract class AbstractLogsMonitor implements LogsMonitor
{
    /** @var array */
    protected $levels = [];
    
    /** @var string */
    protected $minLevel;
    
    /** @var array */
    protected $withContext;
    
    /** @var OutputWriter */
    protected $outputWriter;
    
    /** @var LogConsumer for LogReader */
    protected $consumer;
    
    /**
     * Allows to redefine levels (if different from default).
     * The list of Levels must be ordered from the most to the least significant.
     * Instead of call this method, new levels can be set by pass them as second argument to filterByLevels.
     *
     * @param array $levels
     * @return $this fluent interface
     */
    final public function setLevels(array $levels)
    {
        $this->levels = $levels;
        return $this;
    }
    
    /**
     * List of levels that this LogsMonitor knows and can filter by one of them.
     *
     * @return array
     */
    final public function getLevels()
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
    final public function filterByLevel($level, array $levels = [])
    {
        if ($level !== $this->minLevel) {
            $this->minLevel = $level;
            $this->consumer = null;
        }
    
        if (!empty($levels)) {
            $this->levels = $levels;
            $this->consumer = null;
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
    final public function filterByContext(array $context)
    {
        if ($context !== $this->withContext) {
            $this->withContext = $context;
            $this->consumer = null;
        }
        
        return $this;
    }
    
    /**
     * Call of this method is required before logs are streamed to OutputWriter.
     *
     * @param OutputWriter $outputWriter
     * @return $this fluent interface
     */
    final public function setOutputWriter(OutputWriter $outputWriter)
    {
        if ($outputWriter !== $this->outputWriter) {
            $this->outputWriter = $outputWriter;
            $this->consumer = null;
        }
        
        return $this;
    }
    
    /**
     * @param integer|null $maxNum number of logs to read before return; must be >= 1
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws InvalidArgumentException if param maxNum is invalid
     * @return void
     */
    final protected function assertParamsAreValid($maxNum, $timeout)
    {
        if ($maxNum !== null && (!is_int($maxNum) || $maxNum < 1)) {
            throw new InvalidArgumentException('Invalid parameter maxNum');
        }
        
        if (!is_int($timeout) || $timeout < 0) {
            throw new InvalidArgumentException('Invalid parameter timeout');
        }
    }
    
    /**
     * @throws BadMethodCallException
     * @return void
     */
    final protected function prepareConsumer()
    {
        if ($this->consumer) {
            return;
        }
        
        if (!$this->outputWriter) {
            throw new BadMethodCallException('OutputWriter is not set in '.get_class($this));
        }
        
        $this->consumer = new LogToOutputSender($this->outputWriter);
        
        if ($this->withContext) {
            $this->consumer = new ConsumerFilteredByContext($this->consumer, $this->withContext);
        }
        
        if ($this->minLevel) {
            $this->consumer = new ConsumerFilteredByLevel(
                $this->consumer, $this->minLevel, $this->levels, $this->outputWriter
            );
        }
    }
}