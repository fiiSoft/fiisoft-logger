<?php

namespace FiiSoft\Tools\Logger\Reader\LogsMonitor;

use BadMethodCallException;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use InvalidArgumentException;
use FiiSoft\Tools\Logger\Reader\LogConsumer;
use FiiSoft\Tools\Logger\Reader\LogConsumer\ConsumerFilteredByContext;
use FiiSoft\Tools\Logger\Reader\LogConsumer\ConsumerFilteredByLevel;
use FiiSoft\Tools\Logger\Reader\LogConsumer\LogToOutputSender;
use FiiSoft\Tools\Logger\Reader\LogsMonitor;
use FiiSoft\Tools\Logger\Reader\LogsReader;

final class DefaultLogsMonitor implements LogsMonitor
{
    /** @var LogsReader */
    private $logsReader;
    
    /** @var string */
    private $minLevel;
    
    /** @var array */
    private $withContext;
    
    /** @var OutputWriter */
    private $outputWriter;
    
    /** @var LogConsumer for LogReader */
    private $consumer;
    
    /**
     * @param LogsReader $logsReader
     */
    public function __construct(LogsReader $logsReader)
    {
        $this->logsReader = $logsReader;
    }
    
    /**
     * One can set minimum level of logs that will be streamed to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supossed to be streamed.
     *
     * @param string $level
     * @return $this fluent interface
     */
    public function filterByLevel($level)
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
    public function filterByContext(array $context)
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
    public function setOutputWriter(OutputWriter $outputWriter)
    {
        if ($outputWriter !== $this->outputWriter) {
            $this->outputWriter = $outputWriter;
            $this->consumer = null;
        }
        
        return $this;
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
        if ($maxNum !== null && (!is_int($maxNum) || $maxNum < 1)) {
            throw new InvalidArgumentException('Invalid parameter maxNum');
        } elseif (!is_int($timeout) || $timeout < 0) {
            throw new InvalidArgumentException('Invalid parameter timeout');
        }
        
        if (!$this->consumer) {
            $this->prepareConsumer();
        }
        
        $this->logsReader->read($this->consumer, $maxNum, $timeout);
    }
    
    /**
     * @throws BadMethodCallException
     * @return void
     */
    private function prepareConsumer()
    {
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