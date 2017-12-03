<?php

namespace FiiSoft\Logger\Writer\WriterConstraint;

use Closure;

final class GenericConstraint implements Constraint
{
    /** @var Closure */
    private $func;
    
    /**
     * @param Closure $func this function will get two params: message (string) and context (array) and must return bool
     */
    public function __construct(Closure $func)
    {
        $this->func = $func;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function allowsToWrite($message, array $context)
    {
        $func = $this->func;
        return (bool) $func($message, $context);
    }
}