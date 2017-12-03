<?php

namespace FiiSoft\Logger\Reader\LogsMonitor;

final class InstantLogsMonitor extends AbstractLogsMonitor
{
    /** @var integer|null */
    private $maxReads;
    
    /** @var int */
    private $readsCounter = 0;
    
    /**
     * Start to sending logs to OutputWriter, which has to be set before call this method.
     * If param maxNum is provided, then only given number of messages will be transferred to OutputWriter.
     *
     * @param integer|null $maxNum number of logs to read before return; must be >= 1
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws \BadMethodCallException if OutputWriter was not set before this method is call
     * @throws \InvalidArgumentException if param maxNum or timeout is invalid
     * @return void
     */
    public function start($maxNum = null, $timeout = 0)
    {
        $this->assertParamsAreValid($maxNum, $timeout);
        $this->prepareConsumer();
    
        $this->maxReads = $maxNum;
        $this->readsCounter = 0;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @throws \BadMethodCallException
     * @return void
     */
    public function consumeLog($message, array $context = [])
    {
        if ($this->consumer) {
            if ($this->maxReads === null) {
                $this->consumer->consumeLog($message, $context);
            } elseif ($this->readsCounter++ < $this->maxReads) {
                $this->consumer->consumeLog($message, $context);
            }
        }
    }
}