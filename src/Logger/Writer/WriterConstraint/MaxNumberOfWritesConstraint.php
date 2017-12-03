<?php

namespace FiiSoft\Logger\Writer\WriterConstraint;

use InvalidArgumentException;

final class MaxNumberOfWritesConstraint implements Constraint
{
    /** @var int */
    private $maxNumberOfWrites = 0;
    
    /** @var int */
    private $numberOfWrites = 0;
    
    /**
     * @param int $maxNumberOfWrites must be >= 0
     * @throws InvalidArgumentException
     */
    public function __construct($maxNumberOfWrites = 0)
    {
        $this->setMaxNumberOfWrites($maxNumberOfWrites);
    }
    
    /**
     * @param int $maxNumberOfWrites must be >= 0
     * @throws InvalidArgumentException
     * @return void
     */
    public function setMaxNumberOfWrites($maxNumberOfWrites)
    {
        if (is_int($maxNumberOfWrites) && $maxNumberOfWrites >= 0) {
            $this->maxNumberOfWrites = $maxNumberOfWrites;
        } else {
            throw new InvalidArgumentException('Invalid param maxNumberOfWrites');
        }
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function allowsToWrite($message, array $context)
    {
        return ++$this->numberOfWrites <= $this->maxNumberOfWrites;
    }
}