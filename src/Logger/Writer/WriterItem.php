<?php

namespace FiiSoft\Logger\Writer;

use Exception;

final class WriterItem implements LogsWriter
{
    const MAX_IDLE_TIME = 2 << 12; //in seconds
    const INITIAL_DELAY = 2 << 1; //in seconds
    
    /** @var LogsWriter */
    private $writer;
    
    /** @var bool */
    private $isAlive = true;
    
    /** @var int initial value of delay in seconds */
    private $initialDelay = 0;
    
    /** @var int current delay in seconds */
    private $delay = 0;
    
    /** @var int timestamp of next check if writer is alive */
    private $nextTryAt = 0;
    
    /**
     * @param LogsWriter $writer
     * @param int|null $delay
     */
    public function __construct(LogsWriter $writer, $delay = null)
    {
        $this->writer = $writer;
        $this->initialDelay = $delay ?: self::INITIAL_DELAY;
    }
    
    /**
     * @return void
     */
    public function writerIsDead()
    {
        if ($this->isAlive) {
            $this->isAlive = false;
            $this->delay = $this->initialDelay;
        } elseif ($this->delay < self::MAX_IDLE_TIME) {
            $this->delay <<= 1;
        }
        
        $this->nextTryAt = time() + $this->delay;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        if ($this->isAlive) {
            $this->writer->write($message, $context);
        } elseif (time() >= $this->nextTryAt) {
            $this->writer->write($message, $context);
            $this->isAlive = true;
        }
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function writeIfAlive($message, array $context = [])
    {
        if ($this->isAlive) {
            try {
                $this->writer->write($message, $context);
                return true;
            } catch (Exception $e) {
                //noop
            }
        }
        
        return false;
    }
    
    /**
     * @return LogsWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }
}