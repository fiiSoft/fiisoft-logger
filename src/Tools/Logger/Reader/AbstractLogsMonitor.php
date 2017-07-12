<?php

namespace FiiSoft\Tools\Logger\Reader;

use BadMethodCallException;
use FiiSoft\Tools\Logger\Reader\LogConsumer\ConsumerFilteredByContext;
use FiiSoft\Tools\Logger\Reader\LogConsumer\ConsumerFilteredByLevel;
use FiiSoft\Tools\Logger\Reader\LogConsumer\LogToOutputSender;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use InvalidArgumentException;

abstract class AbstractLogsMonitor implements LogsMonitor
{
    /** @var string */
    protected $minLevel;
    
    /** @var array */
    protected $withContext;
    
    /** @var OutputWriter */
    protected $outputWriter;
    
    /** @var LogConsumer for LogReader */
    protected $consumer;
    
    /**
     * One can set minimum level of logs that will be streamed to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supossed to be streamed.
     *
     * @param string $level
     * @return $this fluent interface
     */
    final public function filterByLevel($level)
    {
        if ($level !== $this->minLevel) {
            $this->minLevel = $level;
            $this->consumer = null;
        }
        
        return $this;
    }
    
    /**
     * One can set context that is required to stream logs to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supossed to be streamed.
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
                $this->consumer, $this->minLevel, [], $this->outputWriter
            );
        }
    }
}