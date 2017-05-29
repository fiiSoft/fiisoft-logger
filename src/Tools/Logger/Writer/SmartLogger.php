<?php

namespace FiiSoft\Tools\Logger\Writer;

use Psr\Log\LoggerInterface;

interface SmartLogger extends LoggerInterface
{
    /**
     * Set prefix for all messages.
     *
     * @param string $prefix
     * @return $this fluent interface
     */
    public function setPrefix($prefix);
    
    /**
     * Set default context appended to all messages.
     * If message is logged with its own context, it will be merged with default one.
     *
     * @param array $context
     * @return $this fluent interface
     */
    public function setContext(array $context);
    
    /**
     * If set then only messages with level equal or greater then minLevel will be logged.
     *
     * @param string $minLevel
     * @return $this fluent interface
     */
    public function setMinLevel($minLevel);
    
    /**
     * Specify order of levels from the most to the least significant
     * (imagine that most important level is on top, and least is on bottom).
     *
     * The hierarchy of levels is important when minLevel of logged messages is set.
     * By default order of levels is the same like Psr has.
     *
     * This method allows to add new levels as well - just put their names in given array.
     *
     * @param array $levels
     * @return $this fluent interface
     */
    public function setOrderOfLevels(array $levels);
}