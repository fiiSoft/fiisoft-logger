<?php

namespace FiiSoft\Logger\SmartLogger;

use FiiSoft\Logger\SmartLogger;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

abstract class AbstractSmartLogger extends AbstractLogger implements SmartLogger
{
    /** @var string */
    protected $prefix = '';
    
    /** @var array */
    protected $context = [];
    
    /** @var int */
    protected $minLevel = 0;
    
    /** @var array */
    protected $levels = [
        LogLevel::EMERGENCY => 7,
        LogLevel::ALERT => 6,
        LogLevel::CRITICAL => 5,
        LogLevel::ERROR => 4,
        LogLevel::WARNING => 3,
        LogLevel::NOTICE => 2,
        LogLevel::INFO => 1,
        LogLevel::DEBUG => 0,
    ];
    
    /**
     * Set prefix for all messages.
     *
     * @param string $prefix
     * @return $this fluent interface
     */
    final public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }
    
    /**
     * Set default context appended to all messages.
     * If message is logged with its own context, it will be merged with default one.
     *
     * @param array $context
     * @return $this fluent interface
     */
    final public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }
    
    /**
     * If set then only messages with level equal or greater then minLevel will be logged.
     *
     * @param string $minLevel
     * @return $this fluent interface
     */
    final public function setMinLevel($minLevel)
    {
        if (isset($this->levels[$minLevel])) {
            $this->minLevel = $this->levels[$minLevel];
        }
        
        return $this;
    }
    
    /**
     * Specify order of levels from the most to the least significant
     * (imagine that most important level is on top, and least is on bottom).
     *
     * The hierarchy of levels is important when minLevel of logged messages is set.
     *
     * This method allows to add new levels as well - just put their names in given array.
     *
     * @param array $levels
     * @return $this fluent interface
     */
    final public function setOrderOfLevels(array $levels)
    {
        if (!empty($levels)) {
            $this->levels = [];
            
            $i = count($levels);
            foreach ($levels as $level) {
                $this->levels[$level] = --$i;
            }
        }
    
        return $this;
    }
}