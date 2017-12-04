<?php

namespace FiiSoft\Logger\Writer\WriterConstraint;

use InvalidArgumentException;
use SplQueue;

final class NumberOfWritesPerTimeConstraint implements Constraint
{
    /** @var int */
    private $maxNumberOfWrites = 0;
    
    /** @var int */
    private $perTimeInSeconds = 1;
    
    /** @var int */
    private $lastWrite = 0;
    
    /** @var SplQueue */
    private $stats;
    
    /**
     * @param int $maxNumberOfWrites must be >= 0 (0 means no writes are allowed at all!)
     * @param int $perTimeInSeconds must be >= 1
     * @throws InvalidArgumentException
     */
    public function __construct($maxNumberOfWrites = 0, $perTimeInSeconds = 1)
    {
        $this->setMaxNumberOfWritesPerTime($maxNumberOfWrites, $perTimeInSeconds);
    }
    
    /**
     * @param int $maxNumberOfWrites must be >= 0
     * @param int $perTimeInSeconds must be >= 1
     * @throws InvalidArgumentException
     * @return void
     */
    public function setMaxNumberOfWritesPerTime($maxNumberOfWrites, $perTimeInSeconds)
    {
        if (is_int($maxNumberOfWrites) && $maxNumberOfWrites >= 0) {
            $this->maxNumberOfWrites = $maxNumberOfWrites;
        } else {
            throw new InvalidArgumentException('Invalid param maxNumberOfWrites');
        }
        
        if (is_int($perTimeInSeconds) && $perTimeInSeconds >= 1) {
            $this->perTimeInSeconds = $perTimeInSeconds;
        } else {
            throw new InvalidArgumentException('Invalid param perTimeInSeconds');
        }
    
        $this->reset();
    }
    
    /**
     * @return void
     */
    private function reset()
    {
        $this->lastWrite = 0;
        $this->stats = new SplQueue();
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function allowsToWrite($message, array $context)
    {
        $current = time();
        
        if ($this->maxNumberOfWrites === 1) {
            if ($this->lastWrite + $this->perTimeInSeconds <= $current) {
                $this->lastWrite = $current;
                return true;
            }
        } elseif ($this->maxNumberOfWrites > 1) {
            $expired = $current - $this->perTimeInSeconds;
        
            while (!$this->stats->isEmpty()) {
                $last = $this->stats->bottom();
                if ($last <= $expired) {
                    $this->stats->dequeue();
                } else {
                    break;
                }
            }
        
            if ($this->stats->isEmpty() || $this->stats->count() <= $this->maxNumberOfWrites) {
                $this->stats->enqueue($current);
                return true;
            }
        
            $this->stats->enqueue($current);
        }
        
        return false;
    }
}